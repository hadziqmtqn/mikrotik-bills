<?php

namespace App\Services;

use App\Enums\BillingType;
use App\Enums\StatusData;
use App\Models\ExtraCost;
use App\Services\CustomerService\CSService;

class ExtraCostService
{
    public static function options(mixed $customerServiceId = null): array
    {
        $customerService = CSService::findById($customerServiceId);

        return ExtraCost::query()
            ->where('is_active', true)
            ->when(($customerService?->status === StatusData::ACTIVE->value), fn($query) => $query->where('billing_type', BillingType::RECURRING->value))
            ->get()
            ->mapWithKeys(function (ExtraCost $extraCost) {
                return [$extraCost->id => [
                    'name' => $extraCost->name,
                    'fee' => $extraCost->fee
                ]];
            })
            ->toArray();
    }
}
