<?php

namespace App\Services;

use App\Models\ServicePackage;
use Illuminate\Database\Eloquent\Builder;

class ServicePackageService
{
    public static function options($planType = null, $serviceType = null, $activeOnly = false): array
    {
        return ServicePackage::query()
            ->when($planType, fn(Builder $query) => $query->where('plan_type', $planType))
            ->when($serviceType, fn(Builder $query) => $query->where('service_type', $serviceType))
            ->when($activeOnly, fn(Builder $query) => $query->where('is_active', true))
            ->get()
            ->mapWithKeys(function (ServicePackage $package) {
                // value bisa gunakan JSON encode, label tetap tampil nama
                return [$package->id => [
                    'name' => $package->package_name,
                    'price' => $package->package_price
                ]];
            })->toArray();
    }
}
