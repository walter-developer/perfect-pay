<?php

namespace App\Models;

use App\Models\{
    Model,
    Contact,
    PersonUser,
    PersonPhysical,
    PersonContact,
    PersonAddress,
};
use Carbon\Carbon;

class Person extends Model
{
    protected $table = 'people';

    protected $columns = [
        'id',
        'name',
        'document',
        'id_people_user',
        'id_people_contact',
        'id_people_adresses',
        'birth_date',
        'phone',
        'cell_phone',
        'email',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'name',
        'document',
        'birth_date',
        'phone',
        'cell_phone',
        'email',
        'id_people_user',
        'id_people_contact',
        'id_people_adresses',
    ];

    protected $casts = [
        'birth_date' => 'date:d-m-Y H:m:s',
        'created_at' => 'date:d-m-Y H:m:s',
        'updated_at' => 'date:d-m-Y H:m:s',
        'deleted_at' => 'date:d-m-Y H:m:s',
    ];


    public function user()
    {
        return $this->hasOne(PersonUser::class, 'id', 'id_people_user');
    }

    public function physical()
    {
        return $this->hasOne(PersonPhysical::class, 'id_people', 'id');
    }

    public function users()
    {
        return $this->hasMany(PersonUser::class, 'id_people', 'id');
    }

    public function address()
    {
        return $this->hasOne(PersonAddress::class, 'id', 'id_people_adresses');
    }

    public function adresses()
    {
        return $this->hasMany(PersonAddress::class, 'id_people', 'id');
    }

    public function contact()
    {
        return $this
            ->hasOneThrough(Contact::class, PersonContact::class, 'id', 'id_people_contact', 'id', 'id_contacts');
    }

    public function contacts()
    {
        return $this
            ->hasManyThrough(Contact::class, PersonContact::class, 'id_people', 'id', 'id', 'id_contacts');
    }


    public function setBirthDateAttribute($value)
    {
        $date = Carbon::parse($value);
        return  $this->setAttributeRaw('birth_date', $date->format('Y-m-d H:m:i'));
    }
}
