<?php

namespace App\Models;

use App\Models\{
    Model,
    Person,
    Contact
};

class PersonContact extends Model
{
    protected $table = 'people_contacts';

    protected $columns = [
        'id',
        'id_people',
        'id_contacts',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'id_people',
        'id_contacts',
    ];

    public function contact()
    {
        return $this->hasOne(Contact::class, 'id', 'id_contacts');
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'id_people', 'id');
    }
}
