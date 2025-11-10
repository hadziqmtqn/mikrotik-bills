<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
