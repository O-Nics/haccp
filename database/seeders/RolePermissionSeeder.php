<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $allPermissions = Permission::pluck('id')->toArray();

        $rolePermissions = [
            'owner' => $allPermissions,
            'manager' => Permission::query()
                ->whereIn('name', [
                    'users.view', 'users.create', 'users.edit',
                    'roles.view',
                    'business.view', 'business.edit',
                    'haccp.view', 'haccp.create', 'haccp.edit', 'haccp.validate',
                    'reports.view', 'reports.export',
                ])
                ->pluck('id')->toArray(),
            'employee' => Permission::query()
                ->whereIn('name', [
                    'business.view',
                    'haccp.view', 'haccp.create',
                    'reports.view',
                ])
                ->pluck('id')->toArray(),
            'viewer' => Permission::query()
                ->whereIn('name', [
                    'business.view',
                    'haccp.view',
                    'reports.view',
                ])
                ->pluck('id')->toArray(),
        ];

        $roles = Role::all();

        foreach ($roles as $role) {
            $permissions = $rolePermissions[$role->name] ?? [];
            $role->permissions()->sync($permissions);
        }
    }
}
