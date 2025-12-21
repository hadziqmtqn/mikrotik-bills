<?php

namespace App\Services\CustomerService;

use App\Models\CustomerService;
use App\Models\ServicePackage;

class CreateCSService
{
    public static function insertCustomerService($userId, ServicePackage $servicePackage, string $packageType = null, string $status = null): CustomerService
    {
        $customerService = new CustomerService();
        $customerService->service_package_id = $servicePackage->id;
        $customerService->user_id = $userId;
        $customerService->daily_price = $servicePackage->daily_price;
        $customerService->price = $servicePackage->package_price;
        $customerService->package_type = $packageType;

        if ($status) {
            $customerService->status = $status;
        }

        $customerService->save();

        return $customerService;
    }
}
