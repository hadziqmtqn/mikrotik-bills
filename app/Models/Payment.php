<?php

namespace App\Models;

use App\Observers\PaymentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[ObservedBy([PaymentObserver::class])]
class Payment extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'slug',
        'serial_number',
        'code',
        'user_id',
        'invoice_id',
        'payment_method',
        'bank_account_id',
        'amount',
        'date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'slug' => 'string',
            'date' => 'date',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    // TODO Attributes
    protected function proofOfPayment(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->hasMedia('proof_of_payment') ? $this->getFirstTemporaryUrl(now()->addHour(), 'proof_of_payment') : null,
        );
    }
}
