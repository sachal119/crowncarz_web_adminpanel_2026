<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingPercentage extends Model
{
    protected $fillable = ['estate', 'mpv', 'seater8', 'executive'];

    protected $casts = [
        'estate' => 'float',
        'mpv' => 'float',
        'seater8' => 'float',
        'executive' => 'float',
    ];
}
