<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['super-admin', 'admin', 'user'] as $item) {
            Role::create([
                'name' => $item,
                'guard_name' => 'web',
            ]);
        }
    }
}
