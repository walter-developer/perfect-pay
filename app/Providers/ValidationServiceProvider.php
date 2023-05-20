<?php

namespace App\Providers;

use App\Support\Validate\Rules\{
    Cast,
    Length,
    Brasil,
    ValDefault
};
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationServiceProvider as ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{

    public function boot()
    {
        Validator::extend('cast', Cast::class);
        Validator::extend('length', Length::class);
        Validator::extend('brasil', Brasil::class);
        Validator::extendImplicit('default', ValDefault::class);
    }
}
