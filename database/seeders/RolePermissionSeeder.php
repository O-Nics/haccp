<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assign permissions to roles
        $rolesPermissions = [
            'owner' => [
                'user.view_any',
                'user.view',
                'user.create',
                'user.update',
                'user.delete',
            ],
            'manager' => [
                'user.view_any',
                'user.view',
                'user.update',
            ],
            'staff' => [
                'user.view',
            ],
        ];

        foreach ($rolesPermissions as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                foreach ($permissions as $permissionName) {
                    $permission = \App\Models\Permission::where('name', $permissionName)->first();
                    if ($permission && ! $role->permissions->contains($permission->id)) {
                        $role->permissions()->attach($permission->id);
                    }
                }
            }
        }
    }
}
