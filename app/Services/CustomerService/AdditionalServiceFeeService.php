<?php

namespace App\Services\CustomerService;

use App\Models\InvCustomerService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdditionalServiceFeeService
{
    public static function handleBulk($customerServiceId, Builder|Collection $extraCosts, ?InvCustomerService $invCustomerService = null): void
    {
        $extraCosts = $extraCosts instanceof Builder
            ? $extraCosts->get()
            : $extraCosts;

        $now = now();

        $rows = $extraCosts->map(fn ($extraCost) => [
            'customer_service_id' => $customerServiceId,
            'extra_cost_id' => $extraCost->id,
            'fee' => $extraCost->fee,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('additional_service_fees')->insert($rows->all());

        $invCustomerService?->update([
            'extra_costs' => $extraCosts->map(fn($extraCost) => [
                'id' => $extraCost->id,
                'name' => $extraCost->name,
                'fee' => $extraCost->fee,
            ])
        ]);
    }
}
