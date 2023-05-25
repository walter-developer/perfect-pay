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

    public function errors(): array
    {
        return $this->errors;
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

    public function createCharge(Collection $charge): Collection
    {

        $newCharge = [
            'id_charge_asaas' => null,
            'id_client_asaas' => $charge->get('id'),
            'charge_status' => EnumChargeStatus::GENERATED
        ];

        $firstCharge = $this->charge->firstOrCreate($newCharge, $newCharge);
        $firstCharge->load('asaasClient');
        $charge = $charge->merge($firstCharge?->toArray() ?: []);
        $response = $this->httpCreateCharge($charge);
        return $response;
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
            'billingType' => $charge?->get('type'),
            'value' => $charge?->get('value'),
            'dueDate' => $charge?->get('due_date'),
            'installmentCount' => $charge?->get('installment_count'),
            'installmentValue' => $charge?->get('installment_value'),
            'discount' => $charge?->get('discount'),
            'interest' => $charge?->get('discount'),
            'fine' => $charge?->get('discount'),
            'split' => $charge?->get('split'),
            'creditCard' => [
                'holderName' => $charge?->get('card.holder_name'),
                'number' => $charge?->get('card.number'),
                'expiryMonth' => $charge?->get('card.expiry_month'),
                'expiryYear' => $charge?->get('card.expiry_year'),
                'ccv' => $charge?->get('card.ccv'),
            ],
            'creditCardHolderInfo' => [
                'name' => $charge?->get('card_info.name'),
                'email' => $charge?->get('card_info.email'),
                'cpfCnpj' => $charge?->get('card_info.document'),
                'postalCode' => $charge?->get('card_info.cep'),
                'addressNumber' => $charge?->get('card_info.number'),
                'addressComplement' => $charge?->get('card_info.address_complement'),
                'phone' => $charge?->get('card_info.phone'),
                'mobilePhone' => $charge?->get('card_info.cell_phone'),
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
                'charge_type' => EnumChargeType::enum($collection->get('billingType', 'UNDEFINED')),
                'charge_status' => EnumChargeStatus::PENDING
            ];
            $first = $this->charge->updateOrCreate($filterCharge, $chargeInsertOrUpdate);
            $success = $collection->merge($first?->toArray() ?: []);
        });

        return $success;
    }
}
