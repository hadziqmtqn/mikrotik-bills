<?php

namespace Database\Seeders\Setting;

use App\Models\Application;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $application = new Application();
        $application->short_name = 'Mikrotik Bills';
        $application->full_name = 'Mikrotik Bills';
        $application->navigation_position = 'left';
        $application->business_name = 'Mikrotik Company';
        $application->business_email = 'mikrotik@company.id';
        $application->business_phone = '+6281234567890';
        $application->save();
    }
}
