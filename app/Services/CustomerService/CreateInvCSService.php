<?php

namespace App\Services\CustomerService;

use App\Models\CustomerService;
use App\Models\InvCustomerService;

class CreateInvCSService
{
    public static function handle($invoiceId, CustomerService $customerService, bool $includeBill): InvCustomerService
    {
        $invCustomerService = new InvCustomerService();
        $invCustomerService->invoice_id = $invoiceId;
        $invCustomerService->customer_service_id = $customerService->id;
        $invCustomerService->amount = $customerService->price;
        $invCustomerService->include_bill = $includeBill;
        $invCustomerService->save();

        return $invCustomerService;
    }
}
