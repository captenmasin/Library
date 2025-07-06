<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\UserRole;
use App\Enums\UserPermission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
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
