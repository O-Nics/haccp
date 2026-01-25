<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RolePermission>
 */
class RolePermissionFactory extends Factory
{
    protected $model = RolePermission::class;

    public function definition(): array
    {
        return [];
    }

    public function withRole(Role|array|null $role = null): static
    {
        $instance = $role instanceof Role
            ? $role
            : Role::factory()->withBusiness()->create(is_array($role) ? $role : []);

        return $this->for($instance, 'role');
    }

    public function withPermission(Permission|array|null $permission = null): static
    {
        $instance = $permission instanceof Permission
            ? $permission
            : Permission::factory()->create(is_array($permission) ? $permission : []);

        return $this->for($instance, 'permission');
    }
}
