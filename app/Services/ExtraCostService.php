<?php

namespace App\Services;

use App\Models\ExtraCost;

class ExtraCostService
{
    public static function options($billingType = null): array
    {
        return ExtraCost::query()
            ->where('is_active', true)
            ->when($billingType, fn($query) => $query->where('billing_type', $billingType))
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
