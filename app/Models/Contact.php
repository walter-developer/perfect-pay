<?php

namespace App\Models;

use App\Models\Model;

class Contact extends Model
{
    protected $table = 'contacts';

    protected $columns = [
        'id',
        'email',
        'phone',
    ];

    protected $fillable = [
        'id',
        'email',
        'phone',
    ];
}
