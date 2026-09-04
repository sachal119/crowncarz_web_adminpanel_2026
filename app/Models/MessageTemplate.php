<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'for', 'content'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class, 'template_id');
    }
}

