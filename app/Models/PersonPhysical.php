<?php

namespace App\Models;

use App\Models\{
    Model,
    Person
};

class PersonPhysical extends Model
{
    protected $table = 'people_physical';

    protected $columns = [
        'id',
        'id_people',
        'father_name',
        'mother_name',
        'general_record',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'id_people',
        'father_name',
        'mother_name',
        'general_record',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'id_people', 'id');
    }
}
