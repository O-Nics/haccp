<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property-read \App\Models\BusinessRole|null $businessRole
 * @property-read \App\Models\Permission|null $permission
 *
 * @method static \Database\Factories\BusinessRolePermissionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessRolePermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessRolePermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessRolePermission query()
 *
 * @mixin \Eloquent
 */
class BusinessRolePermission extends Pivot
{
    use HasFactory;

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    public function businessRole(): BelongsTo
    {
        return $this->belongsTo(BusinessRole::class);
    }
}
