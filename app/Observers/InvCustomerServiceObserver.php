<?php

namespace App\Observers;

use App\Models\InvCustomerService;
use App\Services\CustomerService\RecalculateInvoiceTotalService;

class InvCustomerServiceObserver
{
    public function created(InvCustomerService $invCustomerService): void
    {
        $invCustomerService->refresh();
        $invCustomerService->loadMissing('invoice');

        $this->recalculate($invCustomerService);
    }

    private function recalculate(InvCustomerService $invCustomerService): void
    {
        RecalculateInvoiceTotalService::totalPrice($invCustomerService->invoice);
    }
}
