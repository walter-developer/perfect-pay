<?php

namespace App\Support\Validate\Rules;

use Illuminate\Validation\Validator;
use App\Support\Validate\BaseRules;

class Length extends BaseRules
{

    public function validate(string|int $attribute, mixed $value, array|null $parameters = [], Validator $validator)
    {
        try {
            $isNullable = $validator->hasRule($attribute, ['Nullable']);
            return empty($isNullable && parent::isAbsoluteEmpty($value)) ? $this->case($attribute, $value, $parameters, $validator) : true;
        } catch (\Throwable $e) {
            $validator->setCustomMessages([$attribute . '.length' => 'Falha ao validar o campo :attribute!']);
            return false;
        }
    }

    public function case(string|int $attribute, $value, $parameters, Validator $validator): bool
    {
        $default = 1;
        $parameters = array_filter($parameters);
        $min = array_shift($parameters);
        $max = end($parameters);
        $min = preg_replace("/([^0-9])/", '', $min);
        $max = preg_replace("/([^0-9])/", '', $max);
        $min = (is_numeric($min) ? $min : $default);
        return match (true) {
            is_array($value)  => $this->lengthArray($value, $min, $max, $attribute, $validator),
            is_object($value) => $this->lengthArray($value, $min, $max, $attribute, $validator),
            is_bool($value) => $this->lengthBoolean($value, $min, $max, $attribute, $validator),
            is_string(strval($value)) => $this->lengthString($value, $min, $max, $attribute, $validator),
            default => $this->default($attribute, $validator)
        };
    }

    private function default(string|int $attribute, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.length' => 'Falha ao validar o campo :attribute!'
        ]);
        return false;
    }

    private function lengthString(mixed $value, int|string|null $min, int|string|null $max, string|int $attribute, Validator $validator)
    {
        $length = strlen($value);
        $max = (is_numeric($max) ? $max : $length);
        $min = min([$min, $max]);
        $max = max([$min, $max]);
        if ($length < $min) {
            $validator->setCustomMessages([$attribute . '.length' => "O campo ( :attribute ) deve conter um valor com no minímo $min caracteres!"]);
        }
        if ($length > $max) {
            $validator->setCustomMessages([$attribute . '.length' => "O campo ( :attribute ) deve conter um valor com no máximo $min caracteres!"]);
        }
        return ($length >= $min) && ($length <= $max);
    }

    private function lengthBoolean(mixed $value, int|string|null $min, int|string|null $max, string|int $attribute, Validator $validator)
    {
        $length = strlen($value) ?: 1;
        $max = (is_numeric($max) ? $max : $length);
        $min = min([$min, $max]);
        $max = max([$min, $max]);
        if ($length < $min) {
            $validator->setCustomMessages([$attribute . '.length' => "O campo ( :attribute ) deve conter um valor com no minímo $min caracteres!"]);
        }
        if ($length > $max) {
            $validator->setCustomMessages([$attribute . '.length' => "O campo ( :attribute ) deve conter um valor com no máximo $min caracteres!"]);
        }
        return ($length >= $min) && ($length <= $max);
    }

    private function lengthArray(array|object $value, int|string|null $min, int|string|null $max, string|int $attribute, Validator $validator)
    {
        $length = count((array)$value);
        $max = (is_numeric($max) ? $max : $length);
        $min = min([$min, $max]);
        $max = max([$min, $max]);
        if ($length < $min) {
            $validator->setCustomMessages([$attribute . '.length' => "O campo ( :attribute ) deve conter no minímo $min itens!"]);
        }
        if ($length > $max) {
            $validator->setCustomMessages([$attribute . '.length' => "O campo ( :attribute ) deve conter no máximo $max itens!"]);
        }
        return ($length >= $min) && ($length <= $max);
    }
}
