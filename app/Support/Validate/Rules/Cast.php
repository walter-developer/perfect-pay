<?php

namespace App\Support\Validate\Rules;

use Illuminate\Validation\Validator;
use App\Support\Validate\BaseRules;

class Cast extends BaseRules
{

    public function validate(string|int $attribute, mixed $value, array|null $parameters = [], Validator $validator)
    {
        try {
            $type = strval(trim(strtolower(array_shift($parameters))));
            $isNullable = $validator->hasRule($attribute, ['Nullable']);
            return empty($isNullable && parent::isAbsoluteEmpty($value)) ? $this->case($type, $attribute, $value, $validator) : true;
        } catch (\Throwable $e) {
            $type = array_shift($parameters);
            $validator->setCustomMessages([$attribute . '.brasil'  => 'Falha ao converter o campo :attribute !']);
            return false;
        }
    }

    public function case($type, $attribute, $value, Validator $validator): bool
    {
        return match ($type) {
            'numerico', 'numeric' => $this->numerico($attribute, $value, $validator),
            'regex-numerico', 'regex-numeric' => $this->regexNumerico($attribute, $value, $validator),
            'inteiro', 'integer', 'int' => $this->inteiro($attribute, $value, $validator),
            'flutuante', 'float' => $this->flutuante($attribute, $value, $validator),
            'flutuante-string', 'float-string' => $this->flutuanteString($attribute, $value, $validator),
            'moeda', 'money' => $this->moeda($attribute, $value, $validator),
            'lista', 'array', '[]' => $this->array($attribute, $value, $validator),
            'boleano', 'boolean', 'bool' => $this->boleano($attribute, $value, $validator),
            'quilometragem', 'km_h', 'km', => $this->quilometragem($attribute, $value, $validator),
            'maiuscula', 'maiúscula', 'uppercase', 'upper' => $this->caixaAlta($attribute, $value, $validator),
            'minuscula', 'minúscula', 'lowercase', 'lower' => $this->caixaBaixa($attribute, $value, $validator),
            'texto', 'text' => $this->texto($attribute, $value, $validator),
            'texto-maiusculo', 'texto-maiúsculo', 'text-uppercase' => $this->textoCaixaAlta($attribute, $value, $validator),
            'texto-minusculo', 'texto-minúsculo', 'text-lowercase' => $this->textoCaixaBaixa($attribute, $value, $validator),
            'remover-espacos', 'sem-espacos', 'remove-spaces', 'no-spaces' => $this->semEspacos($attribute, $value, $validator),
            'alfa-numericos', 'alfanumericos', 'alpha-numerics', 'alphanumerics' => $this->alfaNumerico($attribute, $value, $validator),
            'alfa-numericos-maiusculo', 'alfanumericos-maiusculo', 'alpha-numerics-uppercase', 'alphanumerics-uppercase' => $this->alfaNumericoCaixaAlta($attribute, $value, $validator),
            'alfa-numericos-minusculos', 'alfanumericos-minusculos', 'alpha-numerics-lowercase', 'alphanumerics-lowercase' => $this->alfaNumericoCaixaBaixa($attribute, $value, $validator),
            'texto-maiúsculo-sem-espacos', 'texto-maiusculo-sem-espacos', 'uppercase-no-spaces', 'text-uppercase-no-spaces' => $this->textoCaixaAlta($attribute, $value, $validator),
            'texto-minúsculo-sem-espacos', 'texto-minusculo-sem-espacos', 'uppercase-no-spaces', 'text-uppercase-no-spaces' => $this->textoCaixaBaixa($attribute, $value, $validator),
            default => $this->default($attribute, $type, $validator)
        };
    }

    private function default(string $type, string|int $attribute, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.brasil'  => 'Falha ao validar o campo :attribute, validação:[ ' . $type . ' ]!'
        ]);
        return false;
    }

    private function quilometragem(string|int $attribute, mixed $value, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.cast' =>
            'O campo ( :attribute )  é inválido, falha ao converter o valor do campo em numério, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!'
        ]);
        $data = $validator->getData();
        $newvalue = parent::toKm($value);
        $newvalue = (is_null($newvalue) ? $value : $newvalue);
        $data = parent::setValue($data, $attribute,  $newvalue);
        $validator->setData($data);
        return strlen($newvalue) > 0;
    }

    private function numerico(string|int $attribute, mixed $value, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.cast' =>
            'O campo ( :attribute )  é inválido, falha ao converter o valor do campo em numério, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!'
        ]);
        $data = $validator->getData();
        $newvalue = parent::toNumeric($value);
        $newvalue = (is_null($newvalue) ? $value : $newvalue);
        $data = parent::setValue($data, $attribute,  $newvalue);
        $validator->setData($data);
        return !is_null($newvalue);
    }

    private function regexNumerico(string|int $attribute, mixed $value, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.cast' =>
            'O campo ( :attribute )  é inválido, falha ao converter o valor do campo em numério, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!'
        ]);
        $data = $validator->getData();
        $newvalue = parent::onlyNumbers($value);
        $newvalue = (is_null($newvalue) ? $value : $newvalue);
        $data = parent::setValue($data, $attribute,  $newvalue);
        $validator->setData($data);
        return !is_null($newvalue);
    }

    private function inteiro(string|int $attribute, mixed $value, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.cast' =>
            'O campo ( :attribute )  é inválido, falha ao converter o valor do campo em inteiro, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!'
        ]);
        $data = $validator->getData();
        $newvalue = parent::toNumericInt($value);
        $newvalue = (is_null($newvalue) ? $value : $newvalue);
        $data = parent::setValue($data, $attribute,  $newvalue);
        $validator->setData($data);
        return !is_null($newvalue);
    }

    private function flutuante(string|int $attribute, mixed $value, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.cast' =>
            'O campo ( :attribute )  é inválido, falha ao converter o valor do campo em flutuante, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!'
        ]);
        $data = $validator->getData();
        $newvalue = parent::stringToFloat($value);
        $newvalue = (is_null($newvalue) ? $value : $newvalue);
        $data = parent::setValue($data, $attribute,  $newvalue);
        $validator->setData($data);
        return !is_null($newvalue);
    }

    private function flutuanteString(string|int $attribute, mixed $value, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.cast' =>
            'O campo ( :attribute )  é inválido, falha ao converter o valor do campo em flutuante, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!'
        ]);
        $data = $validator->getData();
        $newvalue = parent::floatToString($value);
        $newvalue = (is_null($newvalue) ? $value : $newvalue);
        $data = parent::setValue($data, $attribute,  $newvalue);
        $validator->setData($data);
        return !is_null($newvalue);
    }

    private function moeda(string|int $attribute, mixed $value, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.cast' =>
            'O campo ( :attribute )  é inválido, falha ao converter o valor do campo em moeda, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!'
        ]);
        $data = $validator->getData();
        $newvalue = parent::floatToMoneyBrasil($value);
        $newvalue = (is_null($newvalue) ? $value : $newvalue);
        $data = parent::setValue($data, $attribute,  $newvalue);
        $validator->setData($data);
        return !is_null($newvalue);
    }

    private function array(string|int $attribute, mixed $value, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.cast' =>
            'O campo ( :attribute )  é inválido, falha ao converter o valor do campo em lista, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!'
        ]);
        $data = $validator->getData();
        $newvalue = parent::toArray($value);
        $newvalue = (is_null($newvalue) ? $value : $newvalue);
        $data = parent::setValue($data, $attribute,  $newvalue);
        $validator->setData($data);
        return !is_null($newvalue);
    }

    private function boleano(string|int $attribute, mixed $value, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.cast' =>
            'O campo ( :attribute )  é inválido, falha ao converter o valor do campo em valor booleano, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!'
        ]);
        $data = $validator->getData();
        $newvalue = parent::toBoolean($value);
        $newvalue = (is_null($newvalue) ? $value : $newvalue);
        $data = parent::setValue($data, $attribute,  $newvalue);
        $validator->setData($data);
        return !is_null($newvalue);
    }

    private function caixaAlta(string|int $attribute, mixed $value, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.cast' =>
            'O campo ( :attribute )  é inválido, falha ao converter para maiúsculo o valor do campo, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!'
        ]);
        $data = $validator->getData();
        $newvalue = parent::toUpperCase($value);
        $newvalue = (is_null($newvalue) ? $value : $newvalue);
        $data = parent::setValue($data, $attribute,  $newvalue);
        $validator->setData($data);
        return !is_null($newvalue);
    }

    private function caixaBaixa(string|int $attribute, mixed $value, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.cast' =>
            'O campo ( :attribute )  é inválido, falha ao converter para minúsculo o valor do campo, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!'
        ]);
        $data = $validator->getData();
        $newvalue = parent::toLowerCase($value);
        $newvalue = (is_null($newvalue) ? $value : $newvalue);
        $data = parent::setValue($data, $attribute,  $newvalue);
        $validator->setData($data);
        return !is_null($newvalue);
    }

    private function semEspacos(string|int $attribute, mixed $value, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.cast' =>
            'O campo ( :attribute )  é inválido, falha ao remover espaços do valor do campo, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!'
        ]);
        $data = $validator->getData();
        $newvalue = parent::toRemoveSpaces($value);
        $newvalue = (is_null($newvalue) ? $value : $newvalue);
        $data = parent::setValue($data, $attribute,  $newvalue);
        $validator->setData($data);
        return !is_null($newvalue);
    }


    private function alfaNumerico(string|int $attribute, mixed $value, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.cast' =>
            'O campo ( :attribute )  é inválido, falha ao remover caracteres do valor do campo, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!'
        ]);
        $data = $validator->getData();
        $newvalue = parent::toAlphaNumeric($value);
        $newvalue = (is_null($newvalue) ? $value : $newvalue);
        $data = parent::setValue($data, $attribute,  $newvalue);
        $validator->setData($data);
        return !is_null($newvalue);
    }

    private function alfaNumericoCaixaAlta(string|int $attribute, mixed $value, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.cast' =>
            'O campo ( :attribute )  é inválido, falha ao remover caracteres e converter para maiúsculo o valor do campo, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!'
        ]);
        $data = $validator->getData();
        $newvalue = parent::toAlphaNumericUpperCase($value);
        $newvalue = (is_null($newvalue) ? $value : $newvalue);
        $data = parent::setValue($data, $attribute,  $newvalue);
        $validator->setData($data);
        return !is_null($newvalue);
    }

    private function alfaNumericoCaixaBaixa(string|int $attribute, mixed $value, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.cast' =>
            'O campo ( :attribute )  é inválido, falha ao remover caracteres e converter para minúsculo o valor do campo, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!'
        ]);
        $data = $validator->getData();
        $newvalue = parent::toAlphaNumericLowerCase($value);
        $newvalue = (is_null($newvalue) ? $value : $newvalue);
        $data = parent::setValue($data, $attribute,  $newvalue);
        $validator->setData($data);
        return !is_null($newvalue);
    }

    private function texto(string|int $attribute, mixed $value, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.cast' =>
            'O campo ( :attribute )  é inválido, falha ao converter o valor do campo para texto, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!'
        ]);
        $data = $validator->getData();
        $newvalue = parent::toText($value);
        $newvalue = (is_null($newvalue) ? $value : $newvalue);
        $data = parent::setValue($data, $attribute,  $newvalue);
        $validator->setData($data);
        return !is_null($newvalue);
    }

    private function textoCaixaAlta(string|int $attribute, mixed $value, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.cast' =>
            'O campo ( :attribute )  é inválido, falha ao converter o valor do campo para texto maiúsculo, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!'
        ]);
        $data = $validator->getData();
        $newvalue = parent::toTextUpperCase($value);
        $newvalue = (is_null($newvalue) ? $value : $newvalue);
        $data = parent::setValue($data, $attribute,  $newvalue);
        $validator->setData($data);
        return !is_null($newvalue);
    }

    private function textoCaixaBaixa(string|int $attribute, mixed $value, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.cast' =>
            'O campo ( :attribute )  é inválido, falha ao converter o valor do campo para texto minúsculo, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!'
        ]);
        $data = $validator->getData();
        $newvalue = parent::toTextLowerCase($value);
        $newvalue = (is_null($newvalue) ? $value : $newvalue);
        $data = parent::setValue($data, $attribute,  $newvalue);
        $validator->setData($data);
        return !is_null($newvalue);
    }

    private function caixaBaixaSemEspaco(string|int $attribute, mixed $value, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.cast' =>
            'O campo ( :attribute )  é inválido, falha ao remover espaços do valor do campo e converte-lo para minúsculo, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!'
        ]);
        $data = $validator->getData();
        $newvalue = parent::toRemoveSpacesAndLowerCase($value);
        $newvalue = (is_null($newvalue) ? $value : $newvalue);
        $data = parent::setValue($data, $attribute,  $newvalue);
        $validator->setData($data);
        return !is_null($newvalue);
    }

    private function caixaBaixaAltaEspaco(string|int $attribute, mixed $value, Validator $validator)
    {
        $validator->setCustomMessages([
            $attribute . '.cast' =>
            'O campo ( :attribute )  é inválido, falha ao remover espaços do valor do campo e converte-lo para maiúsculo, verifique o tipo(s) de dado(s) enviado(s) e tente novamente!'
        ]);
        $data = $validator->getData();
        $newvalue = parent::toRemoveSpacesAndUpperCase($value);
        $newvalue = (is_null($newvalue) ? $value : $newvalue);
        $data = parent::setValue($data, $attribute,  $newvalue);
        $validator->setData($data);
        return !is_null($newvalue);
    }
}
