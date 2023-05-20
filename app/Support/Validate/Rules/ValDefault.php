<?php

namespace App\Support\Validate\Rules;

use Illuminate\Validation\Validator;
use App\Support\Validate\BaseRules;

class ValDefault extends BaseRules
{
    public function validate(string|int $attribute, mixed $value, array|null $parameters = [], Validator $validator)
    {
        try {
            $default = trim(strtolower(current($parameters)));
            if (parent::isGenericEmpty($value)) {
                return $this->defaultValue($default, $parameters, $attribute, $validator);
            }
            return true;
        } catch (\Throwable $e) {
            $validator->setCustomMessages([$attribute . '.default' => 'Falha ao atribuir um valor default ao campo :attribute']);
            return false;
        }
    }

    private function defaultValue(mixed $default, array $parameters, string|int $attribute, Validator $validator)
    {
        $data = $validator->getData();
        if (count($parameters) === 1) {
            $compare = trim(strtolower($default));
            if (is_numeric($compare) || is_float($compare) || in_array($compare, ['null', '[]', 'array'])) {
                $default = eval("return " . $default . ";");
                $data = parent::setValue($data, $attribute, $default);
                $validator->setData($data);
                return true;
            }
            $data[$attribute] = $default;
            $validator->setData($data);
            return true;
        }
        $default = $parameters;
        $data = parent::setValue($data, $attribute, $default);
        $validator->setData($data);
        return true;
    }
}
