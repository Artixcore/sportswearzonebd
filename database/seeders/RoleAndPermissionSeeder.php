<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'manage products',
            'manage categories',
            'manage inventory',
            'manage orders',
            'manage pos',
            'manage sales',
            'manage customers',
            'view reports',
            'manage settings',
            'manage roles',
            'view activity logs',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $ceo = Role::firstOrCreate(['name' => 'ceo', 'guard_name' => 'web']);
        $ceo->syncPermissions(Permission::all());

        $cto = Role::firstOrCreate(['name' => 'cto', 'guard_name' => 'web']);
        $cto->syncPermissions(Permission::all());
    }
}
