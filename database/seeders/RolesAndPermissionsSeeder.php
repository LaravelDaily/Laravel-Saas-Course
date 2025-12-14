<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'users.viewAny',
            'users.create',
            'users.delete',
            'tasks.viewAny',
            'tasks.view',
            'tasks.create',
            'tasks.update',
            'tasks.delete',
            'projects.viewAny',
            'projects.view',
            'projects.create',
            'projects.update',
            'projects.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $superAdminRole = Role::firstOrCreate(['name' => RoleEnum::SuperAdmin->value, 'guard_name' => 'web']);
        $superAdminRole->syncPermissions($permissions);

        $adminRole = Role::firstOrCreate(['name' => RoleEnum::Admin->value, 'guard_name' => 'web']);
        $adminRole->syncPermissions($permissions);

        $userRole = Role::firstOrCreate(['name' => RoleEnum::User->value, 'guard_name' => 'web']);
        $userRole->syncPermissions([
            'tasks.viewAny',
            'tasks.view',
            'tasks.create',
            'tasks.update',
            'projects.viewAny',
            'projects.view',
            'projects.create',
            'projects.update',
        ]);

        $viewerRole = Role::firstOrCreate(['name' => RoleEnum::Viewer->value, 'guard_name' => 'web']);
        $viewerRole->syncPermissions([
            'tasks.viewAny',
            'tasks.view',
            'projects.viewAny',
            'projects.view',
        ]);
    }
}
