<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BusinessRole;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class BusinessRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Assign permissions to restaurant-specific roles
     */
    public function run(): void
    {
        $rolePermissions = [
            'gerant' => [
                // Full restaurant access
                'restaurant_role.*',
                'restaurant_user.*',
                'temperature.*',
                'cleaning.*',
                'reception.*',
                'report.*',
                'settings.*',
            ],
            'chef_cuisine' => [
                // Kitchen management
                'restaurant_user.view_any',
                'restaurant_user.view',
                'temperature.*',
                'cleaning.view_any',
                'cleaning.view',
                'cleaning.create',
                'reception.*',
                'report.view',
                'report.export',
            ],
            'cuisinier' => [
                // Kitchen staff
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
            'serveur' => [
                // Front of house
                'cleaning.view_any',
                'cleaning.view',
                'cleaning.create',
                'report.view',
            ],
            'plongeur' => [
                // Dishwashing/cleaning
                'cleaning.view_any',
                'cleaning.view',
                'cleaning.create',
                'temperature.view',
                'temperature.create',
            ],
        ];

        // Apply permissions to all restaurants' roles
        foreach ($rolePermissions as $roleName => $permissionPatterns) {
            $restaurantRoles = BusinessRole::where('name', $roleName)->get();

            foreach ($restaurantRoles as $role) {
                foreach ($permissionPatterns as $pattern) {
                    // If pattern ends with *, get all permissions matching the prefix
                    if (str_ends_with($pattern, '.*')) {
                        $prefix = str_replace('.*', '', $pattern);
                        $permissions = Permission::where('name', 'like', $prefix.'.%')->get();
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
}
