<?php

namespace App\Models;

use App\Models\{
    Model,
    Person
};

class PersonCompany extends Model
{
    protected $table = 'people_physical';

    protected $columns = [
        'id',
        'id_people',
        'fantasy_name',
        'corporate_name',
        'state_registration',
        'municipal_registration',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'id_people',
        'fantasy_name',
        'corporate_name',
        'state_registration',
        'municipal_registration',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'id_people', 'id');
    }
}
