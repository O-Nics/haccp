<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\RolePermission|null $pivot
 * @property-read Collection<int, \App\Models\Role> $businessRoles
 * @property-read int|null $business_roles_count
 * @property-read Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 *
 * @method static \Database\Factories\PermissionFactory factory($count = null, $state = [])
 * @method static Builder<static>|Permission newModelQuery()
 * @method static Builder<static>|Permission newQuery()
 * @method static Builder<static>|Permission query()
 * @method static Builder<static>|Permission whereCreatedAt($value)
 * @method static Builder<static>|Permission whereDescription($value)
 * @method static Builder<static>|Permission whereDisplayName($value)
 * @method static Builder<static>|Permission whereId($value)
 * @method static Builder<static>|Permission whereName($value)
 * @method static Builder<static>|Permission whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Permission extends Model
{
    use HasFactory;

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id')
            ->using(RolePermission::class);
    }

    public function businessRoles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'business_role_permissions', 'permission_id', 'business_role_id')
            ->using(RolePermission::class);
    }
}
