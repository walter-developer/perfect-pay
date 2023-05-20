<?php

namespace App\Models;

use App\Models\{
    Model,
    Address,
    City
};

class Neighborhood extends Model
{
    protected $table = 'neighborhoods';

    protected $columns = [
        'id',
        'name',
        'id_cities',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'name',
        'id_cities',
    ];

    public function adresses()
    {
        return $this->hasMany(Address::class, 'id_neighborhoods', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'id_cities', 'id');
    }
}
