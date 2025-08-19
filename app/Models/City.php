<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable =
        [
            'name',
            'city_id',
            'is_active',
            'sort',
        ];
    protected $hidden =
        [
            'created_at',
            'updated_at'
        ];
}
