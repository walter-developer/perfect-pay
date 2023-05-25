<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Support\Collection\Collection;
use App\Integrations\ViaCep\ViaCep;
use App\Http\Controllers\Controller;
use App\Integrations\Asaas\Clients as AsaasClients;
use App\Integrations\Asaas\Charges as AsaasCharges;


class TesteController extends Controller
{

    private ViaCep $viaCep;
    private AsaasClients $asaasClients;
    private AsaasCharges $asaasCharges;

    public function __construct(ViaCep $viaCep, AsaasClients $asaasClients, AsaasCharges $asaasCharges)
    {
        $this->viaCep = $viaCep;
        $this->asaasClients = $asaasClients;
        $this->asaasCharges = $asaasCharges;
    }

    public function teste(ViaCep $viaCep, Person $person)
    {
        $newPerson = $person->firstOrCreate(
            [
                'document' => '53232903070',
            ],
            [
                'name' => 'Walter de Padua Junior',
                'document' => '53232903070',
                'birth_date' => '17-06-1994',
                'phone' => '4399604548',
                'cell_phone' => '43999604548',
                'email' => 'teste@teste.com.br',
            ]
        );

        //cadastra cliente
        $newClient = new Collection($newPerson?->toArray() ?: []);
        $newClient->copy('document', 'cpfCnpj');
        $newClient->set('phone', '');
        $newClient->set('mobilePhone', '');
        $newClient->set('phone', '');
        $newClient->set('address', '');
        $newClient->set('addressNumber', '');
        $newClient->set('postalCode', '');
        $newClient->set('notificationDisabled', '');
        $newClient->set('additionalEmails', '');
        $newClient->set('municipalInscription', '');
        $newClient->set('stateInscription', '');
        $newClient->set('observations', '');
        $newClient->set('groupName', '');

        $newClientAsaas = $this->asaasClients->createAsaasClient($newClient)->collect();


        //cadastra cobrança
        $newCharge = new Collection($newClientAsaas?->toArray() ?: []);
        $newCharge->set('type', 'PIX');
        $newCharge->set('type', 'PIX');
        $newCharge->set('value', '10.00');
        $newCharge->set('due_date', '2023-06-10');
        $newCharge->set('description', 'Teste');
        $newCharge->set('externalReference', null);
        $newCharge->set('installment_count', null);
        $newCharge->set('installment_value', null);
        $newCharge->set('interest', null);
        $newCharge->set('fine', null);
        $newCharge->set('split', null);

        $newChargeAsaas = $this->asaasCharges->createCharge($newCharge);

        dd($newChargeAsaas?->toArray());


        //dd('oi to no tste cntroler', $newClient->toArray(), $viaCep->listAddress('87065360'));
    }
}
