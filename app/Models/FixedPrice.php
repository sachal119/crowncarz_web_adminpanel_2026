<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FixedPrice extends Model
{
    protected $table = 'fixed_pricing';

    protected $fillable = [
        'from_postcode', 'to_postcode', 'company_price',
        'driver_price', 'agent_commission', 'type', 'vehicle_type',
        'unique_key',
    ];

    protected $casts = [
        'company_price' => 'float',
        'driver_price' => 'float',
        'agent_commission' => 'float',
    ];

    // The existing live schema stores the base/Saloon fare as company_price.
    // These accessors keep the admin view/export compatible with that schema.
    public function getSaloonAttribute(): float
    {
        return (float) ($this->attributes['company_price'] ?? 0);
    }

    public function getEstateAttribute(): float
    {
        return 0.0;
    }

    public function getMpvAttribute(): float
    {
        return 0.0;
    }

    public function getSeater8Attribute(): float
    {
        return 0.0;
    }

    public function getExecutiveAttribute(): float
    {
        return 0.0;
    }
}
