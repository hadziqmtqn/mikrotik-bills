<?php

namespace App\Models;

use App\Observers\InvoiceObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // TODO Attributes
    protected function totalPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->invoiceItems->sum('amount'),
        );
    }
}
