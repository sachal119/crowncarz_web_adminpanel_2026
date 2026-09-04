<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'status', 'latitude', 'longitude'
    ];



    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class);
    }
}

