<?php

namespace Database\Seeders\Auth;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'super_admin')
            ->first();
        if (!$superAdminRole) {
            $superAdminRole = Role::create(['name' => 'super_admin']);
        }

        $superAdmin = new User();
        $superAdmin->name = 'Super Admin';
        $superAdmin->email = 'superadmin@bkn.my.id';
        $superAdmin->password = Hash::make('superadmin');
        $superAdmin->save();

        $superAdmin->assignRole($superAdminRole);

        $userProfile = new UserProfile();
        $userProfile->user_id = $superAdmin->id;
        $userProfile->whatsapp_number = '081234567890';
        $userProfile->save();
    }
}
