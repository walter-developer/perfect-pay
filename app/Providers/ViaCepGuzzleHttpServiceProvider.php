<?php

namespace App\Providers;

use App\Models\{
    City,
    State,
    Address,
    Country,
    Neighborhood
};
use App\Integrations\ViaCep\ViaCep;
use \Illuminate\Http\Client\Factory;
use Illuminate\Support\ServiceProvider;

class ViaCepGuzzleHttpServiceProvider extends ServiceProvider
{

    public const APP_HTTP_ALIAS = 'AppHttpViaCep';

    public function register()
    {
        $this->app->bindIf(static::APP_HTTP_ALIAS, function ($app) {
            $factory = $app->make(Factory::class);
            $city = $app->make(City::class);
            $state = $app->make(State::class);
            $address = $app->make(Address::class);
            $country = $app->make(Country::class);
            $neighborhood = $app->make(Neighborhood::class);
            $options = [
                'http_errors' => false,
                'verify' => config('viacep.api.ssl', false),
                'force_ip_resolve' => 'v4',
                'connect_timeout' => 500,
                'read_timeout' => 500,
                'timeout' => 500,
            ];
            return (new ViaCep($factory, $country, $state, $city, $neighborhood, $address))
                ->withOptions($options)
                ->contentType('application/json')
                ->acceptJson()
                ->baseUrl(config('viacep.api.url', 'viacep.com.br'));
        });

        $this->app->bindIf(ViaCep::class, function ($app) {
            $factory = $app->make(Factory::class);
            $city = $app->make(City::class);
            $state = $app->make(State::class);
            $address = $app->make(Address::class);
            $country = $app->make(Country::class);
            $neighborhood = $app->make(Neighborhood::class);
            $options = [
                'http_errors' => false,
                'verify' => config('viacep.api.ssl', false),
                'force_ip_resolve' => 'v4',
                'connect_timeout' => 500,
                'read_timeout' => 500,
                'timeout' => 500,
            ];
            return (new ViaCep($factory, $country, $state, $city, $neighborhood, $address))
                ->withOptions($options)
                ->contentType('application/json')
                ->acceptJson()
                ->baseUrl(config('viacep.api.url', 'viacep.com.br'));
        });
    }
}
