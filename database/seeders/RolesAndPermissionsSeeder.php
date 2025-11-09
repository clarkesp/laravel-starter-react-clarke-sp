<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cache (important)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        /*
         * Admin guard roles & permissions
         */
        Permission::firstOrCreate(['guard_name' => 'admin', 'name' => 'manage-admins']);
        Permission::firstOrCreate(['guard_name' => 'admin', 'name' => 'manage-users']);
        Permission::firstOrCreate(['guard_name' => 'admin', 'name' => 'view-metrics']);
        Permission::firstOrCreate(['guard_name' => 'admin', 'name' => 'manage-billing']);

        $superAdmin = Role::firstOrCreate(['guard_name' => 'admin', 'name' => 'super-admin']);
        $admin      = Role::firstOrCreate(['guard_name' => 'admin', 'name' => 'admin']);

        $superAdmin->givePermissionTo([
            'manage-admins',
            'manage-users',
            'view-metrics',
            'manage-billing',
        ]);

        $admin->givePermissionTo([
            'manage-users',
            'view-metrics',
        ]);

        /*
         * Web guard roles & permissions (plans & features)
         */
        Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'feature.pulse']);
        Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'feature.horizon']);
        Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'feature.metrics']);

        $free       = Role::firstOrCreate(['guard_name' => 'web', 'name' => 'user-free']);
        $pro        = Role::firstOrCreate(['guard_name' => 'web', 'name' => 'user-pro']);
        $enterprise = Role::firstOrCreate(['guard_name' => 'web', 'name' => 'user-enterprise']);

        // map features to plans
        $pro->givePermissionTo(['feature.pulse']);
        $enterprise->givePermissionTo(['feature.pulse', 'feature.horizon', 'feature.metrics']);
    }
}
