<?php

namespace Database\Seeders\Service;

use App\Enums\PackageTypeService;
use App\Enums\ServiceType;
use App\Models\ServicePackage;
use App\Models\User;
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
            ->limit(20)
            ->get();

        foreach ($users as $user) {
            $servicePackage = ServicePackage::query()
                ->where('plan_type', $user->userProfile?->account_type)
                ->inRandomOrder()
                ->first();

            if (!$servicePackage) {
                continue;
            }

            $installationDate = now()->subMonth();
            $isPpoe = $servicePackage->service_type === ServiceType::PPPOE->value;

            CreateCSService::handle(
                userId: $user->id,
                servicePackage: $servicePackage,
                packageType: $isPpoe ? PackageTypeService::SUBSCRIPTION->value : PackageTypeService::ONE_TIME->value,
                installationDate: $installationDate,
                status: $faker->randomElement(['pending', 'active'])
            );
        }
    }
}
