<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvExtraCost extends Model
{
    protected $fillable = [
        'invoice_id',
        'extra_cost_id',
        'fee',
    ];
}
