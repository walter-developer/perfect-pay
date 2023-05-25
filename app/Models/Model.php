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

    protected function getAttributeRaw(int|string $key, mixed $default = null): mixed
    {
        if (is_array($this->attributes) && array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
        return $default;
    }

    protected function getAttributeRawCaseValueEmpty(mixed $value = null, int|string|null $key = null): mixed
    {
        if ((is_bool($value) || is_numeric($value)) && strlen($value)) {
            return strlen(strval($value)) ? $value : $this->getAttributeRaw($key);
        }
        if (empty(is_array($value)) && empty(is_object($value))) {
            return strlen(strval($value)) ? $value : $this->getAttributeRaw($key);
        }
        if ((is_array($value) || is_object($value))) {
            return strlen(strval($value)) ? $value : $this->getAttributeRaw($key);
        }
        return $this->getAttributeRaw($key);
    }
}
