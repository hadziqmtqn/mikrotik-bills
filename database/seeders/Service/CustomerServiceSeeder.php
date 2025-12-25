<?php

namespace Database\Seeders\Service;

use App\Enums\PackageTypeService;
use App\Enums\ServiceType;
use App\Models\ExtraCost;
use App\Models\ServicePackage;
use App\Models\User;
use App\Services\CustomerService\AdditionalServiceFeeService;
use App\Services\CustomerService\CreateCSService;
use Faker\Factory;
use Illuminate\Database\Seeder;

class CustomerServiceSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create();

        $users = User::query()
            ->with('userProfile')
            ->whereHas('roles', fn ($q) => $q->where('name', 'user'))
            ->active()
            ->limit(10)
            ->get();

        foreach ($users as $user) {
            $servicePackage = ServicePackage::query()
                ->where('plan_type', $user->userProfile?->account_type)
                ->inRandomOrder()
                ->first();

            if (!$servicePackage) {
                continue;
            }

            $installationDate = now();
            $isSubscription = $servicePackage->service_type === ServiceType::PPPOE->value;

            $customerService = CreateCSService::handle(
                userId: $user->id,
                servicePackage: $servicePackage,
                packageType: $isSubscription ? PackageTypeService::SUBSCRIPTION->value : PackageTypeService::ONE_TIME->value,
                installationDate: $installationDate,
                status: $faker->randomElement(['active', 'pending'])
            );

            if ($isSubscription) {
                AdditionalServiceFeeService::handleBulk(
                    customerServiceId: $customerService->id,
                    extraCosts: ExtraCost::all()
                );
            }
        }
    }
}
