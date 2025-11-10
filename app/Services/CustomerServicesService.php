<?php

namespace App\Services;

use App\Enums\PackageTypeService;
use App\Enums\StatusData;
use App\Models\CustomerService;
use Illuminate\Database\Eloquent\Builder;

class CustomerServicesService
{
    public static function options($userId): array
    {
        return CustomerService::query()
            ->with('servicePackage')
            ->whereHas('user', fn($q) => $q->where('is_active', true))
            ->where([
                'user_id' => $userId,
                'status' => StatusData::ACTIVE->value,
                'package_type' => PackageTypeService::SUBSCRIPTION->value
            ])
            ->where(function (Builder $query) {
                $query->whereHas('invCustomerServices.invoice.payments', function (Builder $query) {
                    $query->where('status', StatusData::PAID->value);
                    $query->whereDate('date', '<=', now()->subMonth()->lastOfMonth());
                });
            })
            ->get()
            ->mapWithKeys(function (CustomerService $customerService) {
                return [$customerService->id => [
                    'name' => $customerService->servicePackage?->package_name,
                    'price' => $customerService->price
                ]];
            })
            ->toArray();
    }
}
