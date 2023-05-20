<?php

namespace App\Http\Controllers\Api;

use App\Models\{
    City,
    State,
    Address,
    Country,
    Neighborhood
};
use \Illuminate\Http\Client\Factory;
use \App\Support\Http\Client as AppHttp;
use \App\Support\Http\Response as AppHttpResponse;

class ViaCep extends AppHttp
{
    private City $city;
    private State $state;
    private Address $address;
    private Country $country;
    private Neighborhood $neighborhood;

    public const COUNTRY = [
        'BR' => 'Brasil'
    ];

    public const STATES = [
        'AC' => 'Acre',
        'AL' => 'Alagoas',
        'AP' => 'Amapá',
        'AM' => 'Amazonas',
        'BA' => 'Bahia',
        'CE' => 'Ceará',
        'DF' => 'Distrito Federal',
        'ES' => 'Espirito Santo',
        'GO' => 'Goiás',
        'MA' => 'Maranhão',
        'MS' => 'Mato Grosso do Sul',
        'MT' => 'Mato Grosso',
        'MG' => 'Minas Gerais',
        'PA' => 'Pará',
        'PB' => 'Paraíba',
        'PR' => 'Paraná',
        'PE' => 'Pernambuco',
        'PI' => 'Piauí',
        'RJ' => 'Rio de Janeiro',
        'RN' => 'Rio Grande do Norte',
        'RS' => 'Rio Grande do Sul',
        'RO' => 'Rondônia',
        'RR' => 'Roraima',
        'SC' => 'Santa Catarina',
        'SP' => 'São Paulo',
        'SE' => 'Sergipe',
        'TO' => 'Tocantins',
    ];


    public function __construct(Factory $factory, Country $country, State $state, City $city, Neighborhood $neighborhood, Address $address)
    {
        $this->factory = $factory;
        $this->country = $country;
        $this->state = $state;
        $this->city = $city;
        $this->address = $address;
        $this->neighborhood = $neighborhood;
    }

    public function listAddress(string $cep): array
    {
        $address = $this->address
            ->with(['neighborhood.city.state.country'])
            ->where('cep', $cep)->first();

        if ($address?->count()) {
            return $address?->toArray() ?: [];
        }

        $this->viacep($cep);
        $address = $this->address
            ->with(['neighborhood.city.state.country'])
            ->where('cep', $cep)->first();

        return $address?->toArray() ?: [];
    }

    private function viacep(string $cep): bool
    {
        $uri = '/ws/##cep##/json/';
        $uri = str_replace('##cep##', $cep, $uri);
        $success = false;
        $this->get($uri)
            ->success(function (AppHttpResponse $response) use (&$success) {
                $collection = $response->collect();
                if ($collection->get('erro', false)) {
                    return $response;
                }
                $success = true;
                $countryData = [
                    'name' => self::COUNTRY['BR'],
                    'acronym' => 'BR'
                ];
                $country = $this->country->firstOrCreate($countryData, $countryData);

                $stateData = [
                    'name' =>  self::STATES[strtoupper($collection->get('uf'))],
                    'acronym' => strtoupper($collection->get('uf')),
                    'id_countries' => $country?->id
                ];
                $state = $this->state->firstOrCreate($stateData, $stateData);

                $cityData = [
                    'name' => $collection->get('localidade'),
                    'acronym' => strtoupper($collection->get('uf')),
                    'ddd' => $collection->get('ddd'),
                    'ibge' => $collection->get('ibge'),
                    'id_states' =>  $state?->id
                ];
                $city = $this->city->firstOrCreate($cityData, $cityData);

                $neighborhoodData = [
                    'name' => $collection->get('bairro'),
                    'id_cities' =>  $city?->id
                ];
                $neighborhood = $this->neighborhood->firstOrCreate($neighborhoodData, $neighborhoodData);

                $addressData = [
                    'address' => $collection->get('logradouro'),
                    'cep' =>  preg_replace('/[^0-9]/', '', $collection->get('cep')),
                    'complement' => $collection->get('complemento'),
                    'id_neighborhoods' =>  $neighborhood?->id
                ];
                $this->address->firstOrCreate($addressData, $addressData);
            });
        return $success;
    }
}
