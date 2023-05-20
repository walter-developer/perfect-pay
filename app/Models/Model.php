<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as MainModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Model extends MainModel
{
    protected $casts = [
        'created_at' => 'date:d-m-Y H:m:s',
        'updated_at' => 'date:d-m-Y H:m:s',
        'deleted_at' => 'date:d-m-Y H:m:s',
    ];

    use SoftDeletes;

    public function setAttributeRaw(int|string $key, mixed $value = null): static
    {
        $this->attributes[$key] =  $value;

        return $this;
    }
}
