<?php

namespace App\Models;

use App\Models\{
    Model,
    AsaasClient
};

class AsaasClientCard extends Model
{
    protected $table = 'asaas_clients_cards';

    protected $columns = [
        'id',
        'alias',
        'token_cache',
        'id_client_asaass',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'alias',
        'token_cache',
        'id_client_asaass',
    ];

    public function asaasClient()
    {
        return $this->belongsTo(AsaasClient::class, 'id_client_asaass', 'id');
    }
}
