<?php

namespace App\Models;

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class PersonUser extends Authenticatable
{
    //use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    use SoftDeletes;

    protected $table = 'people_users';

    protected $columns = [
        'id',
        'id_people',
        'email',
        'password',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'id_people',
        'email',
        'password',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class, 'id_people', 'id');
    }
}
