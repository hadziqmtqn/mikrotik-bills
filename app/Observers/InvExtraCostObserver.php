<?php

namespace App\Observers;

use App\Models\ExtraCost;
use App\Models\InvExtraCost;
use App\Services\RecalculateInvoiceTotalService;

class InvExtraCostObserver
{
    public function saved(InvExtraCost $invExtraCost): void
    {
        $extraCost = ExtraCost::find($invExtraCost->extra_cost_id);
        $invExtraCost->updateQuietly([
            'fee' => $extraCost?->fee ?? 0
        ]);

        $invExtraCost->refresh();
        $invExtraCost->loadMissing('invoice');

        $this->recalculate($invExtraCost);
    }

    public function deleted(InvExtraCost $invExtraCost): void
    {
        $invExtraCost->refresh();

        $this->recalculate($invExtraCost);
    }

    private function recalculate(InvExtraCost $invExtraCost): void
    {
        RecalculateInvoiceTotalService::totalPrice($invExtraCost->invoice);
    }
}
