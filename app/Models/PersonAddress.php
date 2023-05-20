<?php

namespace App\Models;

use App\Models\{
    Model,
    Person,
    Address
};

class PersonAddress extends Model
{
    protected $table = 'people_adresses';

    protected $columns = [
        'id',
        'id_people',
        'id_adresses',
        'number',
        'observation',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'id_people',
        'id_adresses',
        'number',
        'observation',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'id_people', 'id');
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'id', 'id_adresses');
    }
}
