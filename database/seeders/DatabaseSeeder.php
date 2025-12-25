<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\Auth\RoleSeeder;
use Database\Seeders\Auth\SuperAdminSeeder;
use Database\Seeders\Auth\UserSeeder;
use Database\Seeders\Network\RouterSeeder;
use Database\Seeders\Reference\BankAccountSeeder;
use Database\Seeders\Service\CreatePaymentSeeder;
use Database\Seeders\Service\CustomerServiceSeeder;
use Database\Seeders\Service\ExtraCostSeeder;
use Database\Seeders\Service\ServicePackageSeeder;
use Database\Seeders\Setting\ApplicationSeeder;
use Database\Seeders\Setting\InvoiceSettingSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SuperAdminSeeder::class,
            ApplicationSeeder::class,
            RouterSeeder::class,
            UserSeeder::class,
            BankAccountSeeder::class,
            ServicePackageSeeder::class,
            ExtraCostSeeder::class,
            // PAYMENT
            InvoiceSettingSeeder::class,
            // Customer Service Dummy
            CustomerServiceSeeder::class,
            //CreatePaymentSeeder::class
        ]);
    }
}
