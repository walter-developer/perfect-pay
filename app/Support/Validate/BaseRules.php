<?php

namespace App\Support\Validate;

use Illuminate\Http\UploadedFile;

class BaseRules
{
    protected function setValue(array $data, string|int $index, mixed $value = null): array
    {
        $index = explode('.', $index);
        $first = array_shift($index);
        if (is_array($data) && array_key_exists($first, $data) && empty($index)) {
            $data[$first] = $value;
            return $data;
        }
        if (is_array($data) && array_key_exists($first, $data) && count($index)) {
            $index = implode('.', $index);
            $data[$first] = is_array($data[$first]) ? $data[$first] : [];
            $data[$first] = $this->setValue($data[$first], $index, $value);
            return $data;
        }
        return $data;
    }

    protected function getValue(array $data, string $index, mixed $default = null): mixed
    {
        $index = explode('.', $index);
        $first = array_shift($index);
        if (is_array($data) && array_key_exists($first, $data) && empty($index)) {
            return $data[$first];
        }
        if (is_array($data) && array_key_exists($first, $data) && is_array($data[$first]) && !empty($index)) {
            $index = implode('.', $index);
            return $this->getValue($data[$first], $index,  $default);
        }
        return $default;
    }

    protected function isAbsoluteEmpty(mixed $value = null)
    {
        if (is_null($value)) {
            return true;
        }
        if (is_bool($value)) {
            return false;
        }
        if (is_array($value)) {
            return false;
        }
        if (is_object($value)) {
            return false;
        }
        return strlen(strval($value)) <= 0;
    }

    protected function isGenericEmpty(mixed $value = null)
    {
        if (is_null($value)) {
            return true;
        }
        if (is_bool($value)) {
            return false;
        }
        if (is_array($value)) {
            return count((array)$value) <= 0;
        }
        if (is_object($value)) {
            return count((array)$value) <= 0;
        }
        return strlen(strval($value)) <= 0;
    }

    protected function onlyNumbers(string|null $value = null): string|null
    {
        $value = preg_replace("/([^0-9])/", '', (string)$value);
        return (strlen($value) > 0) ? ((string)$value) : null;
    }

    protected function toNumericInt(string|null $value = null): int|null
    {
        $value = preg_replace("/([^(\-?\d+)])/", '', (string)$value);
        return (strlen($value) > 0) ? ((int) $value) : null;
    }

    protected function toNumeric(string|null $value = null): string|null
    {
        $value = preg_replace("/([^(\-?\d+)])/", '', (string)$value);
        return (strlen($value) > 0) ? $value : null;
    }

    protected function toBoolean(mixed $value = null): bool
    {
        if (is_array($value) || is_bool($value)) {
            return !empty($value);
        }
        return in_array(strtoupper($value), ['S', '1', 1, true]);
    }

    protected function toKm(mixed $value = null): string
    {
        $value = preg_replace("/([^0-9])/", '', (string)$value);
        $value = str_pad($value, 10, 0, STR_PAD_LEFT);
        $format = '%d%d%d%d%d%d%d.%d%d%d';
        $newValue = vsprintf($format, str_split($value));
        $newValue = ltrim($newValue, 0);
        return strval($newValue);
    }

    protected function toText(int|string|null $value = null): int|string|null
    {
        $regex = "/[^0-9a-z-A-ZÀÁÃÂÉÊÍÓÕÔÚÜÇÑàáãâéêíóõôúüçñ@\!\#\$\%\&\*\(\)\-\+\s\{\[\]\}\?\.\,\;\|\°\=]/";
        return preg_replace($regex, '', $value);
    }

    protected function toTextUpperCase(int|string|null $value = null): int|string|null
    {
        $regex = "/[^0-9a-z-A-ZÀÁÃÂÉÊÍÓÕÔÚÜÇÑàáãâéêíóõôúüçñ@\!\#\$\%\&\*\(\)\-\+\s\{\[\]\}\?\.\,\;\|\°\=]/";
        return strtoupper(preg_replace($regex, '', $value));
    }

    protected function toTextLowerCase(int|string|null $value = null): int|string|null
    {
        $regex = "/[^0-9a-z-A-ZÀÁÃÂÉÊÍÓÕÔÚÜÇÑàáãâéêíóõôúüçñ@\!\#\$\%\&\*\(\)\-\+\s\{\[\]\}\?\.\,\;\|\°\=]/";
        return strtoupper(preg_replace($regex, '', $value));
    }

    protected function toAlphaNumeric(int|string|null $value = null): int|string|null
    {
        return preg_replace("/[^0-9a-z-A-Z]/", '', $value);
    }

    protected function toAlphaNumericUpperCase(int|string|null $value = null): int|string|null
    {
        return strtoupper(preg_replace("/[^0-9a-z-A-Z]/", '', $value));
    }

    protected function toAlphaNumericLowerCase(int|string|null $value = null): int|string|null
    {
        return strtolower(preg_replace("/[^0-9a-z-A-Z]/", '', $value));
    }

    protected function toRemoveSpaces(int|string|null $value = null): int|string|null
    {
        return preg_replace('/\s+/', '', $value);
    }

    protected function toRemoveSpacesAndUpperCase(int|string|null $value = null): int|string|null
    {
        return strtoupper(preg_replace('/\s+/', '', $value));
    }

    protected function toRemoveSpacesAndLowerCase(int|string|null $value = null): int|string|null
    {
        return strtolower(preg_replace('/\s+/', '', $value));
    }

    protected function toUpperCase(int|string|null $value = null): int|string|null
    {
        return strtolower($value);
    }

    protected function toLowerCase(int|string|null $value = null): int|string|null
    {
        return strtolower($value);
    }

    protected function validateCpfCnpj(string $cpfCnpj): bool
    {
        return ($this->validateCpf($cpfCnpj) || $this->validateCnpj($cpfCnpj));
    }

    protected function validateTelefoneCelular(string $foneCel): bool
    {
        return ($this->validateTelefone($foneCel) || $this->validateCelular($foneCel));
    }

    protected function validateReal(string $real): bool
    {
        return preg_match("/^-?((\d+\.\d{3,3}\,\d{2,2})|(\d{1,3}\,\d{2,2})|(\d+\.\d+)|(\d+))$/", $real);
    }

    protected function validateCep(string $cep): bool
    {
        return preg_match('/^[0-9]{5,5}([-\s]?[0-9]{3,3})?$/', $cep);
    }

    protected function validateTelefone(string $fone): bool
    {
        return preg_match("/^\(?\d{2}\)?\s?\d{4}\-?\d{4}$/", $fone);
    }

    protected function validateCelular(string $celular): bool
    {
        return preg_match("/^\(?\d{2}\)?\s?\d{4,5}\-?\d{4}$/", $celular)
            || preg_match("/^\(?\d{2}\)?\s?\d{1}\s?\d{4,4}\-?\d{4}$/", $celular);
    }

    protected function validateRg(string $rg): bool
    {
        return preg_match('/^(\d{2})\.?(\d{3})\.?(\d{3})\-?(\d{1})$/', $rg)
            || preg_match('/^(\d{2,14})$/', $rg);
    }

    protected function validateNameLastName(int|string|null $name): bool
    {
        $name = strval($name);
        $keyNames = preg_split("/[\s]+/", $name);
        return count($keyNames) > 1;
    }

    protected function validateChavePix(int|string|null $pix): bool
    {
        $chavePix = $this->validateCpfCnpj($pix);
        $chavePix = $chavePix ?: $this->validateTelefoneCelular($pix);
        $chavePix = $chavePix ?: filter_var($pix, FILTER_VALIDATE_EMAIL);
        $chavePixAleatoria = preg_match('/^[a-zA-Z0-9-]+$/', $pix);
        $chavePixAleatoria =  $chavePix && preg_match("/{\-}/", $pix);
        $chavePixAleatoria = $chavePix && (strlen($pix) == 32);
        return ($chavePix || $chavePixAleatoria);
    }

    protected function validateRenavam($renavam): bool
    {
        $renavam = strval($renavam);
        $renavam = preg_replace("/[^0-9]/", '', $renavam);
        $renavam = str_pad($renavam, 11, "0", STR_PAD_LEFT);
        if (!preg_match("/[0-9]{11}/", $renavam)) {
            return false;
        }
        $renavamSemDigito = substr($renavam, 0, 10);
        $renavamReversoSemDigito = strrev($renavamSemDigito);
        $soma = 0;
        $multiplicador = 2;
        for ($i = 0; $i < 10; $i++) {
            $algarismo = substr($renavamReversoSemDigito, $i, 1);
            $soma += $algarismo * $multiplicador;

            if ($multiplicador >= 9) {
                $multiplicador = 2;
            } else {
                $multiplicador++;
            }
        }
        $mod11 = $soma % 11;
        $ultimoDigitoCalculado = 11 - $mod11;
        $ultimoDigitoCalculado = ($ultimoDigitoCalculado >= 10 ? 0 : $ultimoDigitoCalculado);
        $digitoRealInformado = substr($renavam, -1);
        if ($ultimoDigitoCalculado == $digitoRealInformado) {
            return true;
        }
        return false;
    }


    protected function toArray(mixed $value = null): array|null
    {
        try {
            if ($value && is_object($value) && ($value instanceof UploadedFile)) {
                $nome = $value?->getClientOriginalName() ?: null;
                $caminho = $value?->getRealPath() ?: null;
                $tamanho = $value?->getSize() ?: null;
                $extensao = $value?->getClientOriginalExtension() ?: null;
                $mime = $value?->getMimeType() ?: null;
                $base64 = base64_encode($value?->get()) ?: null;
                $erro = $value?->getError() ?: false;
                $mensagem = $value?->getErrorMessage() ?: 'Ok';
                $decode =  [
                    'nome' => $nome,
                    'caminho' => $caminho,
                    'tamanho' => $tamanho,
                    'extensao' => $extensao,
                    'mime' => $mime,
                    'base64' => $base64,
                    'erro' => $erro,
                    'mensagem' => $mensagem
                ];
                return $decode;
            }
            if (is_array($value) || is_object($value)) {
                $decode = json_decode(json_encode($value), true);
                if ((json_last_error() == JSON_ERROR_NONE) && is_array($decode)) {
                    return $decode;
                }
            }
            if (is_string($value)) {
                $decode = json_decode($value, true);
                if ((json_last_error() == JSON_ERROR_NONE) && is_array($decode)) {
                    return $decode;
                }
            }
            return [$value];
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function floatToMoneyBrasil(string|float|int|null $value = null, int $decimal = 2): string|null
    {
        try {
            $value = strval($value);
            $value = preg_replace("/[^0-9\.\,\-]/", '', $value);
            $regexReal = "/^-?(\d+\.\d{3,3}\,\d{1,2})$/";
            $regexMatchs = "/^-?((\d+\.\d{3,3}\,\d{1,2})|(\d+\.\d+)|(\d+\,\d+)|(\d+))$/";
            $match = preg_match($regexMatchs, $value, $matches);
            $matchValue = array_shift($matches) ?: '';
            $matchReal = preg_match($regexReal, $matchValue);
            if ($match && $matchReal && strlen($value) && strlen($matchValue)) {
                $matchValue = str_replace(',', '.', str_replace('.', '', $matchValue));
                return number_format($matchValue, $decimal, ",", ".");
            }
            if ($match && empty($matchReal) && strlen($value) && strlen($matchValue)) {
                $value = str_replace(',', '.', $value);
                $point = strpos(strrev($value), '.') ?: 0;
                $decimal = $decimal ?: $point;
                return number_format($value, $decimal, ",", ".");
            }
            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function floatToString(string|float|int|null $value = null, int $decimal = 2): string|null
    {
        try {
            $value = strval($value);
            $value = preg_replace("/[^0-9\.\,\-]/", '', $value);
            $regexReal = "/^-?(\d+\.\d{3,3}\,\d{1,2})$/";
            $regexMatchs = "/^-?((\d+\.\d{3,3}\,\d{1,2})|(\d+\.\d+)|(\d+\,\d+)|(\d+))$/";
            $match = preg_match($regexMatchs, $value, $matches);
            $matchValue = array_shift($matches) ?: '';
            $matchReal = preg_match($regexReal, $matchValue);
            if ($match && $matchReal && strlen($value) && strlen($matchValue)) {
                $matchValue = str_replace(',', '.', str_replace('.', '', $matchValue));
                return (string)number_format($matchValue, $decimal, '.', '');
            }
            if ($match && empty($matchReal) && strlen($value) && strlen($matchValue)) {
                $matchValue = str_replace(',', '.', $matchValue);
                $point = strpos(strrev($matchValue), '.') ?: 0;
                $decimal = $decimal ?: $point;
                return (string)number_format($matchValue, $decimal, '.', '');
            }
            if (is_float(floatval($value))) {
                $point = strpos(strrev($value), '.') ?: 0;
                $decimal = $decimal ?: $point;
                return (string)number_format($value, $decimal, '.', '');
            }
            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function stringToFloat(string|float|int|null $value = null, int $decimal = 2): float|null
    {
        try {
            $value = strval($value);
            $value = preg_replace("/[^0-9\.\,\-]/", '', $value);
            $regexReal = "/^-?(\d+\.\d{3,3}\,\d{1,2})$/";
            $regexMatchs = "/^-?((\d+\.\d{3,3}\,\d{1,2})|(\d+\.\d+)|(\d+\,\d+)|(\d+))$/";
            $match = preg_match($regexMatchs, $value, $matches);
            $matchValue = array_shift($matches) ?: '';
            $matchReal = preg_match($regexReal, $matchValue);
            if ($match && $matchReal && strlen($value) && strlen($matchValue)) {
                $matchValue = str_replace(',', '.', str_replace('.', '', $matchValue));
                return floatval(number_format($matchValue, $decimal, '.', ''));
            }
            if ($match && empty($matchReal) && strlen($value) && strlen($matchValue)) {
                $matchValue = str_replace(',', '.', $matchValue);
                $point = strpos(strrev($matchValue), '.') ?: 0;
                $decimal = $decimal ?: $point;
                return floatval(number_format($matchValue, $decimal, '.', ''));
            }
            if (is_float(floatval($value))) {
                $point = strpos(strrev($value), '.') ?: 0;
                $decimal = $decimal ?: $point;
                return floatval(number_format($value, $decimal, '.', ''));
            }
            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function validateChassi(string|null $chassi): bool
    {
        try {
            $value = strval($chassi);
            $valueArray = str_split(preg_replace("/[^0-9a-z-A-Z]/", '', $value));
            // Retorna FALSE se:
            // 1 - Se possuir número de dígitos diferente de 17 (alfanuméricos).
            if (count($valueArray) <> 17) {
                return false;
            }
            // Retorna FALSE se:
            // 2 - Se não for string ou possuir o número "0" (ZERO) como 1º dígito.
            $first = current($valueArray);
            if (!is_numeric($first) || ($first == 0)) {
                return false;
            }
            // Retorna FALSE se:
            // 3 - Apresente os caracteres "i", "I", "o", "O", "q", "Q".
            $invalidsChar = ['i', 'I', 'o', 'O', 'q', 'Q'];
            $invalids = array_filter($valueArray, function ($value) use ($invalidsChar) {
                return in_array($value, $invalidsChar);
            });
            if (count($invalids)) {
                return false;
            }
            // Retorna FALSE se:
            //4 - Os quatro últimos caracteres devem ser obrigatoriamente numéricos
            $invalids = array_filter(array_slice($valueArray, -4, 4), function ($value) {
                return empty(is_numeric($value));
            });
            if (count($invalids)) {
                return false;
            }
            // Retorna FALSE se:
            // 5 - Se, a partir do 4º dígito, houver uma repetição consecutiva,
            // por mais de seis vezes, do mesmo dígito (alfabético ou numérico). Exemplos: 9BW11111119452687 e 9BWZZZ5268AAAAAAA.
            $consecutivesCount = 0;
            $validateConsecutive = array_slice($valueArray, 3);
            foreach ($validateConsecutive as $key => $val) {
                $lastInFor = $validateConsecutive[$key - 1] ?? null;
                $consecutivesCount = ($lastInFor == $val) ? ($consecutivesCount + 1) : 1;
                if ($consecutivesCount > 6) {
                    return false;
                }
            }
            //chassi válido
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function validateCpf(string|int|null $cpf): bool
    {
        try {
            // Extrair somente os números
            $cpfList = str_split(preg_replace('/[^0-9]/is', '', strval($cpf)));
            // Verifica se foi informado todos os digitos corretamente
            if (count($cpfList) <> 11) {
                return false;
            }
            // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
            if (preg_match('/(\d)\1{10}/', $cpf)) {
                return false;
            }
            // Faz o calculo para validar o CPF
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += (intval($cpfList[$c]) * intval(($t + 1) - $c));
                }
                $d = (intval(10 * $d) % 11) % 10;
                if (intval($cpfList[$c]) <> intval($d)) {
                    return false;
                }
            }
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function validateCnpj(string|int|null $cnpj): bool
    {
        try {
            $cnpjList =  str_split(preg_replace('/[^0-9]/', '', (string) $cnpj));
            // Valida tamanho
            if (count($cnpjList) <> 14) {
                return false;
            }
            // Valida primeiro dígito verificador
            for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
                $soma += intval($cnpjList[$i]) * $j;
                $j = ($j == 2) ? 9 : $j - 1;
            }
            $resto = $soma % 11;
            if (intval($cnpjList[12]) <> intval($resto < 2 ? 0 : 11 - $resto)) {
                return false;
            }
            // Valida segundo dígito verificador
            for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
                $soma += intval($cnpjList[$i]) * $j;
                $j = ($j == 2) ? 9 : $j - 1;
            }
            $resto = $soma % 11;
            return intval($cnpjList[13]) == intval(($resto < 2 ? 0 : 11 - $resto));
        } catch (\Throwable $e) {
            return false;
        }
    }
}
