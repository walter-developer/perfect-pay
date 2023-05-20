<?php

namespace App\Support\Http;

use Closure;
use Throwable;
use App\Support\Http\Response;
use Illuminate\Http\Client\PendingRequest;
use \GuzzleHttp\Psr7\Utils;
use GuzzleHttp\Psr7\Response as LaravelGuzzleResponse;

class Client extends PendingRequest
{
    private $error;
    private $success;
    private $after;
    private $before;

    public function ip(): string
    {
        $ipaddress = filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP', FILTER_VALIDATE_IP);
        $ipaddress = $ipaddress ?: filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_VALIDATE_IP);
        $ipaddress = $ipaddress ?: filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED', FILTER_VALIDATE_IP);
        $ipaddress = $ipaddress ?: filter_input(INPUT_SERVER, 'HTTP_FORWARDED_FOR', FILTER_VALIDATE_IP);
        $ipaddress = $ipaddress ?: filter_input(INPUT_SERVER, 'HTTP_FORWARDED', FILTER_VALIDATE_IP);
        $ipaddress = $ipaddress ?: filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
        return $ipaddress;
    }

    public function instance(): static
    {
        return $this;
    }

    public function error(Closure $error): static
    {
        $this->error = $error;
        return $this;
    }

    public function success(Closure $success): static
    {
        $this->success = $success;
        return $this;
    }

    public function after(Closure $after): static
    {
        $this->after = $after;
        return $this;
    }

    public function before(Closure $before): static
    {
        $this->before = $before;
        return $this;
    }

    public function header(string $key, mixed $value = null): static
    {
        return tap($this, function () use ($key, $value) {
            $this->options['headers'] = array_merge(($this->options['headers'] ?? []), [$key => $value]);
        });
    }

    protected function prepareUrl(string $uri = '/'): string
    {
        $expressao = '/(\/)\1+/';
        $replace = '$1';
        $newurl = preg_replace($expressao, $replace, $uri);
        return str_ireplace(['http:/', 'https:/'], ['http://', 'https://'], $newurl);
    }

    protected function getOptionsRequest(array $options = []): array
    {
        $options = empty($options) ? parent::getOptions() : $options;
        $optionsLog = $options;
        if (isset($options[$this->bodyFormat])) {
            if ($this->bodyFormat === 'multipart') {
                $optionsLog[$this->bodyFormat] = $this->parseMultipartBodyFormat($options[$this->bodyFormat]);
            } elseif ($this->bodyFormat === 'body') {
                $optionsLog[$this->bodyFormat] = $this->pendingBody;
            }
            if (is_array($options[$this->bodyFormat])) {
                $optionsLog[$this->bodyFormat] = array_merge(
                    $options[$this->bodyFormat],
                    $this->pendingFiles
                );
            }
        }
        return $optionsLog;
    }

    protected function getUrlRequest(string $url): string
    {
        $url = ltrim(rtrim($this->baseUrl, '/') . '/' . ltrim($url, '/'), '/');
        $url = $this->prepareUrl($url);
        return $url;
    }

    protected function isAttachment(mixed $data): bool
    {
        $mimes = [
            'text/plain',
            'text/html',
            'text/xml',
            'text/css',
            'application/xml',
            'text/javascript',
            'application/json',
            'application/xhtml+xml',
            'application/x-httpd-php',
        ];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (is_string($data) && strlen($data) && empty(is_numeric($data)) && empty(ctype_print($data))) {
            $mime = $finfo->buffer($data);
            return empty(in_array(strtolower(trim($mime)), $mimes));
        }
        if (is_string($data) && strlen($data) && empty(is_numeric($data))) {
            $decode = base64_decode($data, true);
            if ((base64_encode($decode) === $data) && is_string($decode) && empty(ctype_print($decode))) {
                $mime = $finfo->buffer(base64_decode($data));
                return empty(in_array(strtolower(trim($mime)), $mimes));
            }
        }
        return false;
    }


    protected function replaceAttachmentOrAttachmentBase64InArray(array $data, $replace = 'conteúdo do tipo anexo binário/base64.'): array
    {
        array_walk_recursive($data, function (&$value, $key) use ($replace) {
            if ($this->isAttachment($value)) {
                return $value = $replace;
            }
        });
        return $data;
    }

    public function send(string $method, string $url, array $options = []): Response
    {
        $url = $this->getUrlRequest($url);
        $options = $this->getOptionsRequest($options);
        [$this->pendingBody, $this->pendingFiles] = [null, []];

        return retry($this->tries ?? 1, function () use ($method, $url, $options) {
            try {
                $before = $this->before;
                if ($before instanceof Closure) {
                    $before($this);
                }
                $laravelData = $this->parseRequestData($method, $url, $options);
                $buildClient = $this->buildClient()->request(
                    $method,
                    $url,
                    $this->mergeOptions([
                        'laravel_data' => $laravelData,
                        'on_stats' => function ($transferStats) {
                            $this->transferStats = $transferStats;
                        },
                    ], $options)
                );

                return tap($this->responseDefaultHttp($buildClient), function ($response) {
                    $this->error ? $response->error($this->error) : null;
                    $this->success ? $response->success($this->success) : null;
                    $this->after ? $response->after($this->after) : null;
                    $response->cookies = $this->cookies;
                    $response->transferStats = $this->transferStats;
                    if ($this->tries > 1 && !$response->successful()) {
                        $response->throw();
                    }
                });
            } catch (ConnectException $e) {
                $domain = str_ireplace('www.', '', parse_url($url, PHP_URL_HOST));
                $exception = $e->getMessage();
                return $this->responseHttp(
                    404,
                    'Falha ao se conectar via http com api(s) externas - [ ' . $domain . ' ].',
                    ['url' => $url, 'metodo' => $method, 'options' => $options, 'exception' => $exception]
                );
            } catch (Throwable $e) {
                $domain = str_ireplace('www.', '', parse_url($url, PHP_URL_HOST));
                $exception = $e->getMessage();
                return $this->responseHttp(
                    404,
                    'Falha ao se conectar via http com api(s) externas - [ ' . $domain . ' ].',
                    ['url' => $url, 'metodo' => $method, 'options' => $options, 'exception' => $exception]
                );
            }
        }, $this->retryDelay ?? 100);
    }

    public function responseDefaultHttp($buildClient): Response
    {
        return new Response($buildClient);
    }

    public function responseHttp(int $code, string $message, array $data = null, $headers = []): Response
    {
        return new Response($this->responseGuzzleHttp($code, $headers)->withStatus($code, $message)->withBody(Utils::streamFor(json_encode($data))));
    }

    public function responseGuzzleHttp(int $status = 200, array $headers = [], $body = null, string $version = '1.1', string $reason = null): LaravelGuzzleResponse
    {
        return new LaravelGuzzleResponse($status, $headers, $body, $version, $reason);
    }
}
