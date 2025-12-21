<?php

namespace App\Services\CustomerService;

use App\Models\ExtraCost;
use App\Models\InvExtraCost;

class CreateInvExtraCostService
{
    public static function handle($invoiceId, ExtraCost $extraCost): void
    {
        $invExtraCost = new InvExtraCost();
        $invExtraCost->invoice_id = $invoiceId;
        $invExtraCost->extra_cost_id = $extraCost->id;
        $invExtraCost->fee = $extraCost->fee;
        $invExtraCost->save();
    }
}
