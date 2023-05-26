<?php

namespace App\Http\Requests\Web;

use App\Http\Requests\WebFormRequest;
use App\Integrations\ViaCep\ImportCep;
use App\Models\Person;

class FormPaymentPix extends WebFormRequest
{
    public function rules()
    {
        return  [
            'name' => 'required|brasil:nome_sobrenome',
            'document' => 'required|brasil:cpf_cnpj|cast:regex-numeric',
            'birth_date' => 'required|date_format:Y-m-d',
            'email' => $this->email(),
            'phone' => 'required|brasil:fone|cast:regex-numeric',
            'cell_phone' => 'required|brasil:fone_cel|cast:regex-numeric',
            'cep' => $this->cep(),
            'number' => 'required|length:1,5',
            'observation' => 'required|length:3',
            'value' => 'required|brasil:real|cast:float',
            'due_date' => 'required|date_format:Y-m-d',
            'description' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'nome',
            'document' => 'documento',
            'email' => 'e-mail',
            'phone' => 'telefone',
            'cell_phone' => 'celular',
            'cep' => 'cep',
            'number' => 'número',
            'observation' => 'observação',
            'description' => 'descrição',
            'due_date' => 'data de vencimento',
            'value' => 'valor'
        ];
    }

    private function email()
    {
        return [
            'required',
            'email',
            function ($attribute, $value, $fail) {
                $document = preg_replace("/([^0-9])/", '', $this->input('document'));
                $emailInvalid = (new Person())->where('email', $value)->where('document', '<>', $document)->first();
                return !empty($emailInvalid) ? $fail("O campo $attribute, contém um e-mail ultilizado por outra pessoa.") : true;
            }
        ];
    }

    private function cep()
    {
        $viaCep = $this->container->make(ImportCep::class);
        return [
            'required',
            'brasil:cep',
            'cast:regex-numeric',
            function ($attribute, $value, $fail) use ($viaCep) {
                $cep = preg_replace("/([^0-9])/", '', $value);
                $cepImported = $viaCep->listAddress($cep);
                return empty($cepImported) ? $fail("O campo $attribute, não contém um cep válido.") : true;
            }
        ];
    }
}
