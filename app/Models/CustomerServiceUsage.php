<?php

namespace App\Models;

use App\Observers\CustomerServiceUsageObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([CustomerServiceUsageObserver::class])]
class CustomerServiceUsage extends Model
{
    protected $fillable = [
        'customer_service_id',
        'used_since',
        'next_billing_date',
        'daily_price',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'used_since' => 'datetime',
            'next_billing_date' => 'datetime',
        ];
    }

    public function customerService(): BelongsTo
    {
        return $this->belongsTo(CustomerService::class);
    }
}
