<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvCustomerService extends Model
{
    protected $fillable = [
        'invoice_id',
        'customer_service_id',
        'amount',
        'include_bill'
    ];

    protected function casts(): array
    {
        return [
            'include_bill' => 'boolean'
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customerService(): BelongsTo
    {
        return $this->belongsTo(CustomerService::class);
    }
}
