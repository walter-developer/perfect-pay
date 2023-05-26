<?php

namespace App\Http\Requests\Web;

use App\Models\AsaasClientCharge;
use App\Http\Requests\WebFormRequest;
use \Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class FormPaymentSuccess extends WebFormRequest
{
    public function prepareForValidation()
    {
        if (strlen($this->route('payment'))) {
            $this->merge(['payment' => $this->route('payment')]);
        }
    }

    public function rules()
    {
        $asaasClientCharge = AsaasClientCharge::class;
        return  [
            'payment' => "numeric|exists:$asaasClientCharge,id",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if (parent::responseTypeJson()) {
            return throw new HttpResponseException(response()
                ->json(['error' => 'Cobrança não encontrada no sistema!'], 404)
                ->setStatusCode(404, 'Data failed validation.'));
        }
        return throw new HttpResponseException(redirect(route('view.payment'))->withInput()
            ->withErrors(['error' => 'Cobrança não encontrada no sistema!'])
            ->setStatusCode(303, 'Data invalid.'));
    }
}
