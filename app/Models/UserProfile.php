<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'whatsapp_number',
        'province',
        'city',
        'district',
        'village',
        'street',
        'postal_code',
        'lat_long',
    ];

    protected function casts(): array
    {
        return [
            'lat_long' => 'array',
        ];
    }
}
