<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionalServiceFee extends Model
{
    protected $fillable = [
        'customer_service_id',
        'extra_cost_id',
        'fee',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean'
        ];
    }

    public function extraCost(): BelongsTo
    {
        return $this->belongsTo(ExtraCost::class);
    }
}
