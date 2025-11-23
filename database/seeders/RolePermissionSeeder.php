<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Assign permissions to SYSTEM roles
     */
    public function run(): void
    {
        $rolesPermissions = [
            'owner' => [
                // Full access to everything
                'user.*',
                'restaurant.*',
                'restaurant_role.*',
                'restaurant_user.*',
                'temperature.*',
                'cleaning.*',
                'reception.*',
                'report.*',
                'settings.*',
            ],
            'manager' => [
                // Can manage users and view restaurants
                'user.view_any',
                'user.view',
                'user.create',
                'user.update',
                'restaurant.view_any',
                'restaurant.view',
                'restaurant_user.view_any',
                'restaurant_user.view',
                'restaurant_user.invite',
                'restaurant_user.update',
                'temperature.*',
                'cleaning.*',
                'reception.*',
                'report.view',
                'report.export',
                'settings.view',
            ],
            'staff' => [
                // Basic read access
                'user.view',
                'restaurant.view',
                'temperature.view_any',
                'temperature.view',
                'temperature.create',
                'cleaning.view_any',
                'cleaning.view',
                'cleaning.create',
                'reception.view_any',
                'reception.view',
                'reception.create',
                'report.view',
            ],
        ];

        foreach ($rolesPermissions as $roleName => $permissionPatterns) {
            $role = Role::where('name', $roleName)->first();
            if (! $role) {
                continue;
            }

            foreach ($permissionPatterns as $pattern) {
                // If pattern ends with *, get all permissions matching the prefix
                if (str_ends_with($pattern, '.*')) {
                    $prefix = str_replace('.*', '', $pattern);
                    $permissions = Permission::where('name', 'like', $prefix . '.%')->get();
                } else {
                    $permissions = Permission::where('name', $pattern)->get();
                }

                foreach ($permissions as $permission) {
                    if (! $role->permissions->contains($permission->id)) {
                        $role->permissions()->attach($permission->id);
                    }
                }
            }
        }
    }
}
