<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\Auth\RoleSeeder;
use Database\Seeders\Auth\SuperAdminSeeder;
use Database\Seeders\Network\RouterSeeder;
use Database\Seeders\Setting\ApplicationSeeder;
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
            RouterSeeder::class
        ]);
    }
}
