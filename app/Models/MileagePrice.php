<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MileagePrice extends Model
{
    protected $table = 'mileage_pricing';

    protected $fillable = [
        'car_type', 'from_mileage', 'to_mileage',
        'cost_per_mileage', 'minimum_price',
    ];

    protected $casts = [
        'from_mileage' => 'float',
        'to_mileage' => 'float',
        'cost_per_mileage' => 'float',
        'minimum_price' => 'float',
    ];
}
