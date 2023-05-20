<?php

namespace App\Models;

use App\Models\{
    Model,
    State,
    Neighborhood
};

class City extends Model
{
    protected $table = 'cities';

    protected $columns = [
        'id',
        'name',
        'acronym',
        'ibge',
        'ddd',
        'id_states',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'name',
        'acronym',
        'ibge',
        'ddd',
        'id_states',
    ];

    public function state()
    {
        return $this->belongsTo(State::class, 'id_states', 'id');
    }

    public function neighborhoods()
    {
        return $this->hasMany(Neighborhood::class, 'id_cities', 'id');
    }
}
