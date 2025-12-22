<?php

namespace App\Services\CustomerService;

use App\Models\CustomerService;
use App\Models\ServicePackage;

class CreateCSService
{
    /**
     * @param $userId
     * @param ServicePackage $servicePackage
     * @param string|null $packageType
     * @param string|null $status
     * @return CustomerService
     */
    public static function handle($userId, ServicePackage $servicePackage, string $packageType = null, $installationDate = null, string $status = null): CustomerService
    {
        $customerService = new CustomerService();
        $customerService->service_package_id = $servicePackage->id;
        $customerService->user_id = $userId;
        $customerService->daily_price = $servicePackage->daily_price;
        $customerService->price = $servicePackage->package_price;
        $customerService->package_type = $packageType;
        $customerService->installation_date = $installationDate;

        if ($status) {
            $customerService->status = $status;
        }

        $customerService->save();

        return $customerService;
    }
}
