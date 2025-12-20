<?php

namespace App\Models;

use App\Observers\InvExtraCostObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([InvExtraCostObserver::class])]
class InvExtraCost extends Model
{
    protected $fillable = [
        'invoice_id',
        'extra_cost_id',
        'fee',
    ];

    public function extraCost(): BelongsTo
    {
        return $this->belongsTo(ExtraCost::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
