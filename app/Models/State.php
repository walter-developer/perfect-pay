<?php

namespace App\Models;


use App\Models\{
    Model,
    Country,
    City
};
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    use SoftDeletes;

    protected $table = 'states';

    protected $columns = [
        'id',
        'name',
        'acronym',
        'id_countries',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'name',
        'acronym',
        'id_countries',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'id_countries', 'id');
    }

    public function cities()
    {
        return $this->hasMany(City::class, 'id_cities', 'id');
    }
}
