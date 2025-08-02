<?php

namespace Database\Seeders\Auth;

use Illuminate\Database\Seeder;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader;
use League\Csv\UnavailableStream;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * @throws UnavailableStream
     * @throws InvalidArgument
     * @throws Exception
     */
    public function run(): void
    {
        foreach (['super_admin', 'admin', 'user'] as $item) {
            Role::create([
                'name' => $item,
                'guard_name' => 'web',
            ]);
        }

        $rows = Reader::createFromPath(database_path('import/permission.csv'))
            ->setHeaderOffset(0)
            ->setDelimiter(';');

        $permissions = [];

        foreach ($rows as $row) {
            // Simpan atau ambil permission
            $permission = Permission::firstOrCreate(['name' => $row['name']]);

            // Simpan ke array berdasarkan role
            foreach ($row as $column => $value) {
                /**
                 * ```php
                 *
                 * $column = 'super_admin'; // super_admin,admin,user
                 * ```
                 */
                if ($column !== 'name' && strtoupper($value) === 'YES') {
                    $permissions[$column][] = $permission->id;
                }
            }
        }

        // Ambil semua role dari DB
        $roles = Role::whereIn('name', array_keys($permissions))
            ->get();

        // Attach permission ke masing-masing role
        foreach ($roles as $role) {
            $role->permissions()->syncWithoutDetaching($permissions[$role->name] ?? []);
        }
    }
}
