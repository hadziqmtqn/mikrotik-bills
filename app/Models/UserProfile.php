<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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

    // TODO Attachments
    protected function latitude(): Attribute
    {
        return Attribute::make(fn() => $this->lat_long ? $this->lat_long['lat'] : null);
    }

    protected function longitude(): Attribute
    {
        return Attribute::make(fn() => $this->lat_long ? $this->lat_long['long'] : null);
    }
}
