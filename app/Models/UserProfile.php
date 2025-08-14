<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class UserProfile extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'account_type',
        'activation_date',
        'place_name',
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
            'activation_date' => 'date'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
