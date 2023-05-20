<?php

namespace App\Models;

use App\Models\{
    Model,
    Neighborhood,
    PersonAddress
};

class Address extends Model
{
    protected $table = 'adresses';

    protected $columns = [
        'id',
        'cep',
        'address',
        'complement',
        'id_neighborhoods',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'cep',
        'address',
        'complement',
        'id_neighborhoods',
    ];

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class, 'id_neighborhoods', 'id');
    }

    public function peopleAdresses()
    {
        return $this->hasMany(PersonAddress::class, 'id_adresses', 'id');
    }
}
