<?php

namespace App\Models;

use App\Models\{
    Model,
    State
};

class Country extends Model
{
    protected $table = 'countries';

    protected $columns = [
        'id',
        'name',
        'acronym',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'name',
        'acronym',
    ];

    public function states()
    {
        return $this->hasMany(State::class, 'id_countries', 'id');
    }
}
