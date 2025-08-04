<?php

namespace Database\Seeders\Network;

use App\Models\Router;
use Illuminate\Database\Seeder;

class RouterSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            [
                'name' => 'Router 1',
                'ip_address' => '192.0.0.1',
                'description' => 'Main office router',
            ],
            [
                'name' => 'Router 2',
                'ip_address' => '1.1.1.1',
                'description' => 'Backup router',
            ]
        ] as $row) {
            Router::updateOrCreate(
                ['name' => $row['name']],
                [
                    'ip_address' => $row['ip_address'],
                    'description' => $row['description'],
                ]
            );
        }
    }
}
