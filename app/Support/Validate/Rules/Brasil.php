<?php

namespace App\Support\Validate\Rules;

use Illuminate\Validation\Validator;
use App\Support\Validate\BaseRules;

class Brasil extends BaseRules
{
    public function validate(string|int $attribute, mixed $value, array|null $parameters = [], Validator $validator): bool
    {
        try {
            $type = trim(strtolower(array_shift($parameters)));
            $isNullable = $validator->hasRule($attribute, ['Nullable']);
            return empty($isNullable && parent::isAbsoluteEmpty($value)) ? $this->case($type, $attribute, $value, $validator) : true;
        } catch (\Throwable $e) {
            $type = array_shift($parameters);
            $validator->setCustomMessages([$attribute . '.brasil'  => 'Falha ao validar o campo :attribute, validação:[ ' . $type . ' ], erro:[ ' . $e->getMessage() . ' ]!']);
            return false;
        }
    }

    public function case($type, $attribute, $value, Validator $validator): bool
    {
        return match ($type) {
            'cpf' => $this->cpf($attribute, $value, $validator),
            'rg' => $this->rg($attribute, $value, $validator),
            'cnpj' => $this->cnpj($attribute, $value, $validator),
            'cep' => $this->cep($attribute, $value, $validator),
            'real' => $this->real($attribute, $value, $validator),
            'chassi' => $this->chassi($attribute, $value, $validator),
            'renavam' => $this->renavan($attribute, $value, $validator),
            'pix' => $this->pix($attribute, $value, $validator),
            'chave_pix' => $this->pix($attribute, $value, $validator),
            'cel', 'celular' => $this->celular($attribute, $value, $validator),
            'fone', 'telefone' => $this->telefone($attribute, $value, $validator),
            'cpf_cnpj', 'cnpj_cpf' => $this->cpfOuCnpj($attribute, $value, $validator),
            'fone_cel', 'cel_fone' => $this->foneOuCel($attribute, $value, $validator),
            'nome', 'sobrenome', 'nome_sobrenome' => $this->nome($attribute, $value, $validator),
            default => $this->default($attribute, $type, $validator)
        };
    }

    public function cpf(string|int $attribute, mixed $value, Validator $validator): bool
    {
        $chek = parent::validateCpf($value);
        $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um ( CPF ) válido!']);
        return $chek;
    }

    public function rg(string|int $attribute, mixed $value, Validator $validator): bool
    {
        $chek = parent::validateRg($value);
        $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um ( RG ) válido!']);
        return $chek;
    }

    public function cnpj(string|int $attribute, mixed $value, Validator $validator): bool
    {
        $chek = parent::validateCnpj($value);
        $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um ( CNPJ ) válido!']);
        return $chek;
    }

    public function cep(string|int $attribute, mixed $value, Validator $validator): bool
    {
        $chek = parent::validateCep($value);
        $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um ( CEP ) válido!']);
        return $chek;
    }

    public function nome(string|int $attribute, mixed $value, Validator $validator): bool
    {
        $chek = parent::validateNameLastName($value);
        $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um ( Nome ) completo válido!']);
        return $chek;
    }

    public function real(string|int $attribute, mixed $value, Validator $validator): bool
    {
        $chek = parent::validateReal($value);
        $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um valor monetário do tipo ( Real R$ ) válido!']);
        return $chek;
    }

    public function cpfOuCnpj(string|int $attribute, mixed $value, Validator $validator): bool
    {
        $chek = parent::validateCpfCnpj($value);
        $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um valor ( CPF/CNPJ ) válido!']);
        return $chek;
    }

    public function celular(string|int $attribute, mixed $value, Validator $validator): bool
    {
        $chek = parent::validateCelular($value);
        $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um numero de ( Celular ) válido!']);
        return $chek;
    }

    public function telefone(string|int $attribute, mixed $value, Validator $validator): bool
    {
        $chek = parent::validateTelefone($value);
        $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um numero de ( Telefone ) válido!']);
        return $chek;
    }

    public function foneOuCel(string|int $attribute, mixed $value, Validator $validator): bool
    {
        $chek = parent::validateTelefoneCelular($value);
        $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um numero de ( Telefone/Celular ) válido!']);
        return $chek;
    }

    public function chassi(string|int $attribute, mixed $value, Validator $validator): bool
    {
        $chek = parent::validateChassi($value);
        $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um numero de ( Chassi ) válido!']);
        return $chek;
    }

    public function renavan(string|int $attribute, mixed $value, Validator $validator): bool
    {
        $chek = parent::validateRenavam($value);
        $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é um numero de ( Renavam ) válido!']);
        return $chek;
    }

    public function pix(string|int $attribute, mixed $value, Validator $validator): bool
    {
        $chek = parent::validateChavePix($value);
        $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute não é uma ( Chave Pix ) válida!']);
        return $chek;
    }

    public function default(string|int $attribute, string|int|null $type, Validator $validator): bool
    {
        $validator->setCustomMessages([$attribute . '.brasil' => 'O campo :attribute contém uma regra de validação [ ' . $type . ' ] não registrada!']);
        return false;
    }
}
