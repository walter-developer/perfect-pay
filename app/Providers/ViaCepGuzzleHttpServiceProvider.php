<?php

namespace App\Providers;

use App\Support\Http\Client as AppHttp;
use \Illuminate\Http\Client\Factory;
use Illuminate\Support\ServiceProvider;

class ViaCepGuzzleHttpServiceProvider extends ServiceProvider
{

    public const APP_HTTP_ALIAS = 'AppHttp';

    public function register()
    {
        $this->app->bindIf(static::APP_HTTP_ALIAS, function ($app) {
            $factory = $app->make(Factory::class);
            $options = [
                'http_errors' => false,
                'verify' => config('viacep.api.ssl', false),
                'force_ip_resolve' => 'v4',
                'connect_timeout' => 500,
                'read_timeout' => 500,
                'timeout' => 500,
            ];
            return (new AppHttp($factory))
                ->withOptions($options)
                ->contentType('application/json')
                ->acceptJson()
                ->baseUrl(config('viacep.api.url', 'viacep.com.br'));
        });

        $this->app->bindIf(AppHttp::class, function ($app) {
            $factory = $app->make(Factory::class);
            $options = [
                'http_errors' => false,
                'verify' => config('viacep.api.ssl'),
                'force_ip_resolve' => 'v4',
                'connect_timeout' => 500,
                'read_timeout' => 500,
                'timeout' => 500,
            ];
            return (new AppHttp($factory))
                ->withOptions($options)
                ->contentType('application/json')
                ->acceptJson()
                ->baseUrl(config('viacep.api.url', 'viacep.com.br'));
        });
    }
}
