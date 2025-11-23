<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property-read \App\Models\Permission|null $permission
 * @property-read \App\Models\Role|null $role
 *
 * @method static \Database\Factories\RolePermissionFactory factory($count = null, $state = [])
 * @method static Builder<static>|RolePermission newModelQuery()
 * @method static Builder<static>|RolePermission newQuery()
 * @method static Builder<static>|RolePermission query()
 *
 * @mixin Eloquent
 */
class RolePermission extends Pivot
{
    use HasFactory;

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }
}
