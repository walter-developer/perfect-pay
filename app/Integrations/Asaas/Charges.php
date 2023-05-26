<?php

namespace App\Integrations\Asaas;

use App\Enumerations\EnumChargeStatus;
use App\Models\AsaasClientCharge;
use App\Integrations\Asaas\Asaas;
use App\Enumerations\EnumChargeType;
use App\Support\Collection\Collection;
use \App\Support\Http\Response as AppHttpResponse;

class Charges
{

    private array $errors;
    private Asaas $http;
    private AsaasClientCharge $charge;

    public function __construct(Asaas $asaas, AsaasClientCharge $charge)
    {
        $this->http = $asaas;
        $this->charge = $charge;
    }

    protected function singleLevelArray(array $data = [])
    {
        $singleLevelArray = [];
        array_walk_recursive($data, function ($value, $key) use (&$singleLevelArray) {
            if (!empty($value) && !is_array($value) && in_array($key, ['description', 'descricao'])) {
                $singleLevelArray[] = $value;
            }
        });
        return array_filter($singleLevelArray);
    }

    public function errors(): array
    {
        return $this->singleLevelArray($this->errors);
    }

    public function listQrcodePix(int $idCharge): Collection
    {
        $charge = $this->charge
            ->where('id', $idCharge)
            ->where('charge_type', EnumChargeType::PIX)->first();
        $response = new Collection($charge?->toArray());

        if ($response->count()) {
            $responseQrcode = $this->httpListQrcodePix($response->get('id_charge_asaas'));
            $response = $response->merge($responseQrcode?->toArray());
        }
        return $response;
    }

    public function listCharge(int $idCharge): Collection
    {
        $charge = $this->charge
            ->where('id', $idCharge)->first();
        $response = new Collection($charge?->toArray());

        if ($response->count()) {
            $responseCharge = $this->httpListCharge($response->get('id_charge_asaas'));
            $response = $response->merge($responseCharge?->toArray());
        }
        return $response;
    }

    public function createCharge(Collection $charge): Collection
    {

        $newCharge = [
            'id_charge_asaas' => null,
            'id_client_asaas' => $charge->get('id'),
            'charge_type' => EnumChargeType::enum($charge->get('type'))->value(),
            'charge_status' => EnumChargeStatus::GENERATED->value(),
            'due_date' => $charge->get('due_date'),
            'value' => $charge->get('value'),
            'description' => $charge->get('description'),
        ];

        $firstCharge = $this->charge->firstOrCreate($newCharge, $newCharge);
        $firstCharge->load('asaasClient.person.address.address');
        $charge = $charge->merge($firstCharge?->toArray() ?: []);
        $response = $this->httpCreateCharge($charge);
        return $response;
    }

    private function httpListCharge(string $idChargeAsaas)
    {
        $uri = '/payments/##id##';
        $uri = str_replace('##id##', $idChargeAsaas, $uri);
        $http = $this->http->get($uri);
        $success = new Collection();

        $http->error(function (AppHttpResponse $response) {
            $this->errors = $response->collect()->toArray();
        });

        $http->success(function (AppHttpResponse $response) use (&$success) {
            $collection = $response->collect();
            $success = $collection;
        });
        return $success;
    }

    private function httpListQrcodePix(string $idChargeAsaas)
    {
        $uri = '/payments/##id##/pixQrCode';
        $uri = str_replace('##id##', $idChargeAsaas, $uri);
        $http = $this->http->get($uri);
        $success = new Collection();

        $http->error(function (AppHttpResponse $response) {
            $this->errors = $response->collect()->toArray();
        });

        $http->success(function (AppHttpResponse $response) use (&$success) {
            $collection = $response->collect();
            $success = $collection;
        });

        return $success;
    }

    public function httpCreateCharge(Collection $charge): Collection
    {
        $uri = '/payments';
        $success = new Collection();
        $body = [
            'externalReference' => $charge?->get('id'),
            'customer' => $charge?->get('asaas_client.id_client_asaas'),
            'billingType' => EnumChargeType::enum($charge?->get('charge_type', 1))->name(),
            'value' => $charge?->get('value'),
            'dueDate' => $charge?->get('due_date'),
            'installmentCount' => $charge?->get('installment_count'),
            'installmentValue' => $charge?->get('installment_value'),
            'discount' => $charge?->get('discount'),
            'interest' => $charge?->get('discount'),
            'fine' => $charge?->get('discount'),
            'split' => $charge?->get('split'),
            'creditCard' => [
                'holderName' => $charge?->get('card.holder_name', $charge?->get('asaas_client.person.name')),
                'number' => $charge?->get('card.number'),
                'expiryMonth' => $charge?->get('card.expiry_month'),
                'expiryYear' => $charge?->get('card.expiry_year'),
                'ccv' => $charge?->get('card.ccv'),
            ],
            'creditCardHolderInfo' => [
                'name' => $charge?->get('asaas_client.person.name', $charge?->get('card.holder_name')),
                'email' => $charge?->get('asaas_client.person.email'),
                'cpfCnpj' => $charge?->get('asaas_client.person.document'),
                'postalCode' => $charge?->get('asaas_client.person.address.address.cep'),
                'addressNumber' => $charge?->get('asaas_client.person.address.number'),
                'addressComplement' => $charge?->get('asaas_client.person.address.observation'),
                'phone' => $charge?->get('asaas_client.person.phone'),
                'mobilePhone' => $charge?->get('asaas_client.person.cell_phone'),
            ]
        ];

        $http = $this->http->post($uri, $body);

        $http->error(function (AppHttpResponse $response) {
            $this->errors = $response->collect()->toArray();
        });

        $http->success(function (AppHttpResponse $response) use (&$success, $charge) {
            $collection = $response->collect();

            $filterCharge = [
                'id' => $collection->get('externalReference', $charge?->get('id')),
                'id_client_asaas' => $charge?->get('asaas_client.id'),
            ];
            $chargeInsertOrUpdate = [
                'id_client_asaas' => $charge?->get('asaas_client.id'),
                'id_charge_asaas' => $collection->get('id'),
                'charge_type' => EnumChargeType::enum($collection->get('billingType', 'UNDEFINED'))->value(),
                'charge_status' => EnumChargeStatus::enum($collection->get('status', 'PENDING'))->value(),
                'due_date' => $charge->get('due_date'),
                'value' => $charge->get('value'),
                'description' => $charge->get('description'),
            ];
            $first = $this->charge->updateOrCreate($filterCharge, $chargeInsertOrUpdate);
            $success = $collection->merge($first?->toArray() ?: []);
        });

        return $success;
    }
}
