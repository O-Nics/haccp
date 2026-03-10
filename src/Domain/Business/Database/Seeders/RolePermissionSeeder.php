<?php

declare(strict_types=1);

namespace Domain\Business\Database\Seeders;

use Domain\Business\Enums\PermissionEnum;
use Domain\Business\Enums\RoleNameEnum;
use Domain\Business\Models\Permission;
use Domain\Business\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Assigns permissions to every role across all businesses using PermissionEnum values.
     * The mapping is keyed by RoleNameEnum so there are no magic strings.
     *
     * owner    → all permissions
     * manager  → members (read + invite + edit) + roles (read) + full product management
     * employee → members (read) + products (read + create)
     * viewer   → members (read) + products (read)
     *
     * @return void
     */
    public function run(): void
    {
        $allPermissionIds = Permission::query()->pluck('id')->toArray();

        $managerPermissionIds = Permission::query()
            ->whereIn('can', [
                PermissionEnum::MEMBERS_VIEW_ANY->value,
                PermissionEnum::MEMBERS_VIEW->value,
                PermissionEnum::MEMBERS_INVITE->value,
                PermissionEnum::MEMBERS_EDIT->value,
                PermissionEnum::ROLES_VIEW_ANY->value,
                PermissionEnum::ROLES_VIEW->value,
                PermissionEnum::PRODUCT_VIEW_ANY->value,
                PermissionEnum::PRODUCT_VIEW->value,
                PermissionEnum::PRODUCT_CREATE->value,
                PermissionEnum::PRODUCT_EDIT->value,
                PermissionEnum::PRODUCT_DELETE->value,
            ])
            ->pluck('id')
            ->toArray();

        $employeePermissionIds = Permission::query()
            ->whereIn('can', [
                PermissionEnum::MEMBERS_VIEW_ANY->value,
                PermissionEnum::MEMBERS_VIEW->value,
                PermissionEnum::PRODUCT_VIEW_ANY->value,
                PermissionEnum::PRODUCT_VIEW->value,
                PermissionEnum::PRODUCT_CREATE->value,
            ])
            ->pluck('id')
            ->toArray();

        $viewerPermissionIds = Permission::query()
            ->whereIn('can', [
                PermissionEnum::MEMBERS_VIEW_ANY->value,
                PermissionEnum::MEMBERS_VIEW->value,
                PermissionEnum::PRODUCT_VIEW_ANY->value,
                PermissionEnum::PRODUCT_VIEW->value,
            ])
            ->pluck('id')
            ->toArray();

        $permissionsByRoleName = [
            RoleNameEnum::OWNER->value    => $allPermissionIds,
            RoleNameEnum::MANAGER->value  => $managerPermissionIds,
            RoleNameEnum::EMPLOYEE->value => $employeePermissionIds,
            RoleNameEnum::VIEWER->value   => $viewerPermissionIds,
        ];

        /** @var Collection<int, Role> $roles */
        $roles = Role::all();

        foreach ($roles as $role) {
            $permissionIds = $permissionsByRoleName[$role->name] ?? [];
            $role->permissions()->sync($permissionIds);
        }
    }
}
