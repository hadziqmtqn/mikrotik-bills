<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerServiceUsage extends Model
{
    protected $fillable = [
        'customer_service_id',
        'invoice_id',
        'period_start',
        'period_end',
        'next_billing_date',
        'days_of_usage',
        'daily_price',
        'total_price',
        'mark_done'
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'datetime',
            'period_end' => 'datetime',
            'next_billing_date' => 'datetime',
            'mark_done' => 'boolean'
        ];
    }

    public function customerService(): BelongsTo
    {
        return $this->belongsTo(CustomerService::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
