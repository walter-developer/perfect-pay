<?php

namespace App\Models;

use App\Models\{
    Model,
    Person,
    AsaasClientCharge
};

class AsaasClient extends Model
{
    protected $table = 'asaas_clients';

    protected $columns = [
        'id',
        'id_people',
        'id_client_asaas',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'id_people',
        'id_client_asaas',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'id_people', 'id');
    }

    public function asaasClientCharge()
    {
        return $this->hasMany(AsaasClientCharge::class, 'id_client_asaass', 'id');
    }
}
