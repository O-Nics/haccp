<?php

namespace Database\Factories;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Database\Eloquent\Factories\Factory;

class RolePermissionFactory extends Factory
{
    protected $model = RolePermission::class;

    public function definition(): array
    {
        return [
        ];
    }

    public function withRole(Role $role): Factory
    {
        return $this->state(function (array $attributes) use ($role) {
            return [
                'role_id' => $role->id,
            ];
        });
    }

    public function withPermission(Permission $permission): Factory
    {
        return $this->state(function (array $attributes) use ($permission) {
            return [
                'permission_id' => $permission->id,
            ];
        });
    }
}
