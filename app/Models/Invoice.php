<?php

namespace App\Models;

use App\Observers\InvoiceObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
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

    public function invCustomerServices(): HasMany
    {
        return $this->hasMany(InvCustomerService::class, 'invoice_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    public function invExtraCosts(): HasMany
    {
        return $this->hasMany(InvExtraCost::class, 'invoice_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // TODO Attributes
    protected function totalPrice(): Attribute
    {
        $invCustomerServiceTotal = $this->invCustomerServices->sum('amount');
        $invExtraCostTotal = $this->invExtraCosts->sum('fee');

        return Attribute::make(
            get: fn() => $invCustomerServiceTotal + $invExtraCostTotal,
        );
    }

    // TODO Scope
    #[Scope]
    protected function filterByStatus(Builder $query, $status): Builder
    {
        return $query->where('status', $status);
    }
}
