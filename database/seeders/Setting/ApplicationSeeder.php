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
        $application->save();
    }
}
