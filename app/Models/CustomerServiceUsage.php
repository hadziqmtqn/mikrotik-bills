<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerServiceUsage extends Model
{
    protected $fillable = [
        'customer_service_id',
        'invoice_id',
        'used_since',
        'next_billing_date',
        'days_of_usage',
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
