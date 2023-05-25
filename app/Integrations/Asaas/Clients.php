<?php

namespace App\Integrations\Asaas;

use App\Models\AsaasClient;
use App\Support\Collection\Collection;
use \App\Support\Http\Response as AppHttpResponse;


class Clients
{
    private array $errors;
    private Asaas $http;
    private AsaasClient $client;

    public function __construct(Asaas $asaas, AsaasClient $client)
    {
        $this->http = $asaas;
        $this->client = $client;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function createAsaasClient(Collection $client): Collection
    {
        $firstClient = $this->client
            ->whereHas('person', fn ($q) => $q->where('document', $client->get('document')))->first() ?: new Collection();

        if ($firstClient?->count()) {
            return $client->merge($firstClient?->toArray() ?: []);
        }

        $this->httpCreateAsaasClient($client);

        $firstClient = $this->client
            ->whereHas('person', fn ($q) => $q->where('document', $client->get('document')))->first() ?: new Collection();

        return $client->merge($firstClient?->toArray() ?: []);
    }

    private function httpCreateAsaasClient(Collection $client): Collection
    {
        $uri = '/customers/';
        $success = new Collection();
        $body = [
            'externalReference' => $client?->get('id'),
            'name' => $client?->get('name'),
            'cpfCnpj' => $client?->get('document'),
            'email' => $client?->get('email'),
            'phone' => $client?->get('phone'),
            'mobilePhone' => $client?->get('cell_phone'),
            'address' => $client?->get('address'),
            'addressNumber' => $client?->get('number'),
            'province' => $client?->get('province'),
            'postalCode' => $client?->get('postalCode'),
            'notificationDisabled' => $client?->get('notification_disabled', false),
            'additionalEmails' => $client?->get('add_emails'),
            'municipalInscription' => $client?->get('municipal_registration'),
            'stateInscription' => $client?->get('state_registration'),
            'observations' => $client?->get('observations'),
            'groupName' => $client?->get('group', 'client'),
        ];
        $http = $this->http->post($uri, $body);

        $http->error(function (AppHttpResponse $response) {
            $this->errors = $response->collect()->toArray();
        });

        $http->success(function (AppHttpResponse $response) use (&$success) {
            $collection = $response->collect();
            $client = [
                'id_people' => $collection->get('externalReference'),
                'id_client_asaas' => $collection->get('id')
            ];
            $first = $this->client->firstOrCreate($client, $client);
            $success = new Collection($first?->toArray() ?: []);
        });

        return $success;
    }
}
