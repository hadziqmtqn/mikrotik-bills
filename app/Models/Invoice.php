<?php

namespace App\Models;

use App\Observers\InvoiceObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([InvoiceObserver::class])]
class Invoice extends Model
{
    protected $fillable = [
        'slug',
        'serial_number',
        'code',
        'user_id',
        'date',
        'due_date',
        'cancel_date',
        'status',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'slug' => 'string',
            'date' => 'datetime',
            'due_date' => 'datetime',
            'cancel_date' => 'datetime',
        ];
    }
}
