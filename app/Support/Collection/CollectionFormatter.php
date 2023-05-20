<?php

namespace App\Support\Collection;

use App\Support\Collection\Collection;

class CollectionFormatter
{

    private const MASK_CEP = "#####-###";
    private const MASK_RG = '##.###.###-#';
    private const MASK_CPF = '###.###.###-##';
    private const MASK_CNPJ = '##.###.###/####-##';
    private const MASK_CELULAR = '#####-####';
    private const MASK_TELEFONE = '####-####';
    private const MASK_DDD_CELULAR = '(##)#####-####';
    private const MASK_DDD_TELEFONE = '(##)####-####';

    private bool $recurssive;
    private string $attribute;
    private object $collection;

    public function __construct(Collection &$collection, string $attribute, bool $recurssive = false)
    {
        $this->attribute = $attribute;
        $this->collection = $collection;
        $this->recurssive = $recurssive;
    }

    public function attribute(): string
    {
        return $this->attribute;
    }

    public function exists(): bool
    {
        return $this?->collection
            ?->exists($this->attribute);
    }

    public function collection(): Collection
    {
        return $this->collection;
    }

    protected function recurssive($method, ...$args): static
    {
        if ($recurssive = $this->recurssive) {
            $collection = $this->collection();
            $collection
                ->foreachInCollections(function ($collection)
                use ($recurssive, $method, $args) {
                    $attribute = $this->attribute();
                    call_user_func_array([$collection?->formatter($attribute, $recurssive), $method], $args ?: []);
                });
        }
        return $this;
    }

    public function value(): mixed
    {
        return $this->collection
            ->exists($this->attribute) ?
            $this->collection->get($this->attribute) : null;
    }

    public function date(string $format = 'd/m/Y'): static
    {
        $collection = $this->collection();
        $value = $this->value();
        $attribute = $this->attribute();
        $exists = $this->exists();
        if ($exists && $value && is_numeric($value) && is_numeric(strtotime($value))) {
            $newValue = $this->unixTimestampToDate($value, $format);
            $collection->set($attribute, $newValue);
            return $this->recurssive(__FUNCTION__, $format);
        }
        if ($exists && $value && $date = date_create($value)) {
            $newValue = date_format($date, $format);
            $collection->set($attribute, $newValue);
        };
        return $this->recurssive(__FUNCTION__, $format);
    }

    public function timestamp(string $format = 'd/m/Y H:i:s'): static
    {
        $collection = $this->collection();
        $value = $this->value();
        $attribute = $this->attribute();
        $exists = $this->exists();
        if ($exists && $value && is_numeric($value) && is_numeric(strtotime($value))) {
            $newValue = $this->unixTimestampToDate($value, $format);
            $collection->set($attribute, $newValue);
            return $this->recurssive(__FUNCTION__, $format);
        }
        if ($exists && $value  && $date = date_create($value)) {
            $newValue = date_format($date, $format);
            $collection->set($attribute, $newValue);
        }
        return $this->recurssive(__FUNCTION__, $format);
    }

    public function numeric(): static
    {
        $collection = $this->collection();
        $value = $this->value();
        $attribute = $this->attribute();
        $exists = $this->exists();
        if ($exists && $value) {
            $newValue = $this->onlyNumbers($value);
            $collection->set($attribute, $newValue);
        }
        return $this->recurssive(__FUNCTION__);
    }

    public function integer(): static
    {
        $collection = $this->collection();
        $value = $this->value();
        $attribute = $this->attribute();
        $exists = $this->exists();
        if ($exists && $value) {
            $newValue = $this->toNumericInt($value);
            $collection->set($attribute, $newValue);
        }
        return $this->recurssive(__FUNCTION__);
    }

    public function float(int $decimal = 2): static
    {
        $collection = $this->collection();
        $value = $this->value();
        $exists = $this->exists();
        $attribute = $this->attribute();
        if ($exists && $value) {
            $newValue = $this->toFloat($value, $decimal);
            $collection->set($attribute, $newValue);
        }
        return $this->recurssive(__FUNCTION__, $decimal);
    }

    public function floatString(int $decimal = 2): static
    {
        $collection = $this->collection();
        $value = $this->value();
        $exists = $this->exists();
        $attribute = $this->attribute();
        if ($exists && $value) {
            $newValue = $this->toFloatString($value, $decimal);
            $collection->set($attribute, $newValue);
        }
        return $this->recurssive(__FUNCTION__, $decimal);
    }

    public function real(int $decimal = 2): static
    {
        $collection = $this->collection();
        $value = $this->value();
        $exists = $this->exists();
        $attribute = $this->attribute();
        if ($exists && $value) {
            $newValue = $this->floatToMoneyBrasil($value, $decimal);
            $collection->set($attribute, $newValue);
        }
        return $this->recurssive(__FUNCTION__, $decimal);
    }

    public function array(): static
    {
        $collection = $this->collection();
        $value = $this->value();
        $attribute = $this->attribute();
        $exists = $this->exists();
        if ($exists && $value) {
            $newValue = $this->toArray($value);
            $collection->set($attribute, $newValue);
        }
        return $this->recurssive(__FUNCTION__);
    }

    public function boolean(): static
    {
        $collection = $this->collection();
        $value = $this->value();
        $attribute = $this->attribute();
        $exists = $this->exists();
        if ($exists) {
            $newValue = $this->toBoolean($value);
            $collection->set($attribute, $newValue);
        }
        return $this->recurssive(__FUNCTION__);
    }

    public function rg(): static
    {
        $collection = $this->collection();
        $value = $this->value();
        $attribute = $this->attribute();
        $exists = $this->exists();
        if ($exists && $value) {
            $newValue = $this->numberToRg($value);
            $collection->set($attribute, $newValue);
        }
        return $this->recurssive(__FUNCTION__);
    }

    public function cpf(): static
    {
        $collection = $this->collection();
        $value = $this->value();
        $attribute = $this->attribute();
        $exists = $this->exists();
        if ($exists && $value) {
            $newValue = $this->numberToCpfCnpj($value);
            $collection->set($attribute, $newValue);
        }
        return $this->recurssive(__FUNCTION__);
    }

    public function cnpj(): static
    {
        $collection = $this->collection();
        $value = $this->value();
        $attribute = $this->attribute();
        $exists = $this->exists();
        if ($exists && $value) {
            $newValue = $this->numberToCpfCnpj($value);
            $collection->set($attribute, $newValue);
        }
        return $this->recurssive(__FUNCTION__);
    }

    public function telefone(): static
    {
        $collection = $this->collection();
        $value = $this->value();
        $attribute = $this->attribute();
        $exists = $this->exists();
        if ($exists && $value) {
            $newValue = $this->numberToFoneCel($value);
            $collection->set($attribute, $newValue);
        }
        return $this->recurssive(__FUNCTION__);
    }

    public function celular(): static
    {
        $collection = $this->collection();
        $value = $this->value();
        $attribute = $this->attribute();
        $exists = $this->exists();
        if ($exists && $value) {
            $newValue = $this->numberToFoneCel($value);
            $collection->set($attribute, $newValue);
        }
        return $this->recurssive(__FUNCTION__);
    }

    public function cep(): static
    {
        $collection = $this->collection();
        $value = $this->value();
        $attribute = $this->attribute();
        $exists = $this->exists();
        if ($exists && $value) {
            $newValue = $this->numberToCep($value);
            $collection->set($attribute, $newValue);
        }
        return $this->recurssive(__FUNCTION__);
    }

    public function __get(mixed $attribute): mixed
    {
        if ($this?->collection?->existis($attribute)) {
            return $this?->collection?->get($attribute);
        }
        return null;
    }

    private function parseNull(mixed $value, mixed $default = null): mixed
    {
        return is_null($value) ? $default : $value;
    }

    private function unixTimestampToDate(string|int $epoch, string $format = 'd/m/Y H:i:s')
    {
        if (is_numeric($epoch) && is_numeric(strtotime($epoch))) {
            $seconds = (strtotime($epoch) / 1000);
            return date($format, $seconds);
        }
        return null;
    }

    private function toBoolean(mixed $value = null): bool
    {
        if (is_string($value) || is_numeric($value) || is_bool($value)) {
            return in_array(strtoupper($value), ['S', '1', 1, true]);
        }
        return !empty($value);
    }

    private function onlyNumbers(string|int|float|null $value = null): string|null
    {
        $value = preg_replace("/([^0-9])/", '', $this->parseNull($value, ''));
        if (strlen($value) > 0) {
            return strval($value);
        }
        return null;
    }

    private function toNumericInt(string|null $value = null): int|null
    {
        $value = preg_replace("/([^(\-?\d+)])/", '', $this->parseNull($value, ''));
        if (strlen($value) > 0) {
            return intval($value);
        }
        return null;
    }

    private function toFloat(string|null $value = null, $decimal = 2): float|null
    {
        $value = preg_replace("/\-?\d*(?:\.\d+)?/", '', $this->parseNull($value, ''));
        if (strlen($value) > 0) {
            return floatval(number_format($value, $decimal, '.', ''));
        }
        return null;
    }

    private function toFloatString(string|null $value = null, int $decimal = 2): string|null
    {
        $value = preg_replace("/\-?\d*(?:\.\d+)?/", '', $this->parseNull($value, ''));
        if (strlen($value) > 0) {
            return strval(number_format($value, $decimal, '.', ''));
        }
        return null;
    }

    private function numberToCep(string|int $value): mixed
    {
        $value = strval(preg_replace("/([^0-9])/", '', $this->parseNull($value, '')));
        if (strlen($value) == 8) {
            return $this->format(self::MASK_CEP, $value);
        }
        return null;
    }

    private function numberToRg(mixed $value): mixed
    {
        $value = strval(preg_replace("/([^0-9])/", '', $this->parseNull($value, '')));
        return $this->format(self::MASK_RG, $value);
    }

    private function numberToCpfCnpj(mixed $value): mixed
    {
        $value = strval(preg_replace("/([^0-9])/", '', $this->parseNull($value, '')));
        if (strlen($value) == 11) {
            return $this->format(self::MASK_CPF, $value);
        }
        if (strlen($value) == 14) {
            return $this->format(self::MASK_CNPJ, $value);
        }
        return null;
    }

    private function numberToFoneCel(mixed $value): mixed
    {
        $value = strval(preg_replace("/([^0-9])/", '', $this->parseNull($value, '')));
        if (strlen($value) == 11) {
            return $this->format(self::MASK_DDD_CELULAR, $value);
        }
        if (strlen($value) == 10) {
            return $this->format(self::MASK_DDD_TELEFONE, $value);
        }
        if (strlen($value) == 9) {
            return $this->format(self::MASK_CELULAR, $value);
        }
        if (strlen($value) == 8) {
            return $this->format(self::MASK_TELEFONE, $value);
        }
        return $value;
    }

    private function format(string $mask, mixed $string, mixed $complete = null, int $leftOrRight = STR_PAD_LEFT): mixed
    {
        if (empty($string)) {
            return $string;
        }
        $strLength = strlen(is_null($string) ? '' : $string);
        $maskLength = substr_count($mask, '#');
        $complete = (empty(is_null($complete)) ? $complete : "#");
        $maxStrLength = (($maskLength > $strLength) ? $maskLength : $strLength);
        $maxMaskLength = (($strLength > $maskLength) ? $strLength : $maskLength);
        $maskPrint = str_pad($mask, $maxMaskLength, '#', $leftOrRight);
        $stringPrint = str_pad($string, $maxStrLength, $complete, $leftOrRight);
        $maskPrint = str_replace('#', '%s', $maskPrint);
        return vsprintf($maskPrint, str_split($stringPrint));
    }

    private function toArray(mixed $value = null): array|null
    {
        if (is_array($value)) {
            return  $value;
        }
        if (is_string($value)) {
            $value = json_decode($value, true);
            $isArray = is_array($value);
            $isArray =  $isArray && (json_last_error() == JSON_ERROR_NONE);
            return  $isArray ? $value : null;
        }
        return null;
    }

    private function floatToMoneyBrasil(string|float|int|null $value = null, int $decimal = 2): string|null
    {
        $value = strval($this->parseNull($value, ''));
        $value = preg_replace("/[^0-9\.\,\-]/", '', $value);
        $regexReal = "/^-?(\d+\.\d{3,3}\,\d{1,2})$/";
        $regexMatchs = "/^-?((\d+\.\d+)|(\d+\,\d+)|(\d+))$/";
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
    }
}
