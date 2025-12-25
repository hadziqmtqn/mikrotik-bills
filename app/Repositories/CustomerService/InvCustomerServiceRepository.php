<?php

namespace App\Repositories\CustomerService;

use App\Models\InvCustomerService;

class InvCustomerServiceRepository
{
    public static function totalBilling($customerServiceId, $invoiceId): int
    {
        $invCustomerService = InvCustomerService::query()
            ->where([
                'customer_service_id' => $customerServiceId,
                'invoice_id' => $invoiceId
            ])
            ->first();

        $total = 0;

        if ($invCustomerService?->include_bill) {
            $extraCost = 0;
            foreach ($invCustomerService->extra_costs as $extra_cost) {
                $extraCost += $extra_cost['fee'] ?? 0;
            }

            $total = $invCustomerService->amount + $extraCost;
        }

        return $total;
    }
}