<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'type', 'template_id', 'status', 'sent_at'
    ];

    protected $dates = ['sent_at'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function template()
    {
        return $this->belongsTo(MessageTemplate::class, 'template_id');
    }
}

