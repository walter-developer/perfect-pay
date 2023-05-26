<?php

namespace App\Http\Requests\Web;

use App\Http\Requests\WebFormRequest;
use App\Integrations\ViaCep\ImportCep;
use App\Models\Person;
use Carbon\Carbon;

class FormPaymentCard extends WebFormRequest
{
    public function rules()
    {
        return  [
            'name' => 'required|brasil:nome_sobrenome',
            'document' => 'required|brasil:cpf_cnpj|cast:regex-numeric',
            'birth_date' => 'required|date_format:Y-m-d|before:' . Carbon::now()->subYears(18)->format('Y-m-d'),
            'email' => $this->email(),
            'phone' => 'required|brasil:fone|cast:regex-numeric',
            'cell_phone' => 'required|brasil:fone_cel|cast:regex-numeric',
            'cep' => $this->cep(),
            'number' => 'required|length:1,5',
            'observation' => 'required|length:3',
            'value' => 'required|brasil:real|cast:float',
            'due_date' => 'required|date_format:Y-m-d|after_or_equal:starting_date',
            'description' => 'required',
            'payment.card.name' => 'required|brasil:nome_sobrenome',
            'payment.card.number' => 'required|length:13,16|cast:regex-numeric',
            'payment.card.expiration' => 'required|date_format:m/y',
            'payment.card.ccv' => 'required|numeric|length:1,3',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'nome',
            'document' => 'documento',
            'birth_date' => 'data de nascimento',
            'email' => 'e-mail',
            'phone' => 'telefone',
            'cell_phone' => 'celular',
            'cep' => 'cep',
            'number' => 'número',
            'observation' => 'observação',
            'description' => 'descrição',
            'due_date' => 'data de vencimento',
            'value' => 'valor',
            'payment.card.name' => 'proprietário do cartão',
            'payment.card.number' => 'numero do cartão',
            'payment.card.expiration' => 'data expiração cartão',
            'payment.card.ccv' => 'código de verificação do cartão'
        ];
    }

    public function messages()
    {
        return [
            'due_date.after_or_equal' => 'O campo :attribute  deve ser uma data posterior ou igual a data atual.',
            'birth_date.before' => 'O campo :attribute  deve ser uma data inferior a data ' . Carbon::now()->subYears(18)->format('d/m/Y'),
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
