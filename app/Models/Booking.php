<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'ref_no', 'passenger_id', 'driver_id', 'vehicle_id', 'payment_type',
        'pickup_address', 'via_addresses', 'dropoff_address', 'status', 'price'
    ];

    protected $casts = [
        'via_addresses' => 'array',
    ];

    public function passenger()
    {
        return $this->belongsTo(Passenger::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }



}

