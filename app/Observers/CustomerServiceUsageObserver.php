<?php

namespace App\Observers;

use App\Models\CustomerService;
use App\Models\CustomerServiceUsage;
use App\Services\InvoiceSettingService;

class CustomerServiceUsageObserver
{
    public function creating(CustomerServiceUsage $customerServiceUsage): void
    {
        $nextRepetitionDate = InvoiceSettingService::nextRepetitionDate();
        $customerServiceUsage->loadMissing('customerService');

        $customerService = $customerServiceUsage->customerService ?? CustomerService::find($customerServiceUsage->customer_service_id);

        $customerServiceUsage->next_billing_date = $nextRepetitionDate;
    }
}
