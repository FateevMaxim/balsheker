<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliverySignoff extends Model
{
    use HasFactory;

    protected $fillable = [
        'express_sn',
        'package_sn',
        'height',
        'width',
        'length',
        'volume',
        'weight',
        'freight_price',
        'freight_cost'
    ];
}
