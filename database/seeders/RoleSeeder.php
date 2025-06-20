<?php

namespace Database\Seeders;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate([
            'name' => UserRole::Admin->value,
        ]);

        $userRole = Role::firstOrCreate([
            'name' => UserRole::User->value,
        ]);

        $userPermissions = [];

        $adminPermissions = [
            Permission::firstOrCreate(['name' => UserPermission::CREATE_USER]),
            Permission::firstOrCreate(['name' => UserPermission::VIEW_ADMIN_PANEL]),
            Permission::firstOrCreate(['name' => UserPermission::VIEW_HORIZON_PANEL]),
            Permission::firstOrCreate(['name' => UserPermission::VIEW_PULSE_PANEL]),
            Permission::firstOrCreate(['name' => UserPermission::VIEW_ANALYTICS]),
        ];

        $adminRole->syncPermissions($adminPermissions);
        $userRole->syncPermissions($userPermissions);
    }
}
