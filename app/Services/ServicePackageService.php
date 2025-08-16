<?php

namespace App\Services;

use App\Models\ServicePackage;
use App\Models\UserProfile;

class ServicePackageService
{
    public static function dropdownOptions($userId): array
    {
        $userProfile = UserProfile::where('user_id', $userId)
            ->first();

        if (!$userProfile) {
            return [];
        }

        return ServicePackage::planType($userProfile->account_type)
            ->active()
            ->mapWithKeys(function (ServicePackage $package) {
                // value bisa gunakan JSON encode, label tetap tampil nama
                return [$package->id => [
                    'name' => $package->package_name,
                    'price' => $package->package_price
                ]];
            })->toArray();
    }
}
