<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property-read \App\Models\Permission|null $permission
 * @property-read \App\Models\RestaurantRole|null $restaurantRole
 *
 * @method static \Database\Factories\RestaurantRolePermissionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantRolePermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantRolePermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantRolePermission query()
 *
 * @mixin \Eloquent
 */
class RestaurantRolePermission extends Pivot
{
    use HasFactory;

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    public function restaurantRole(): BelongsTo
    {
        return $this->belongsTo(RestaurantRole::class);
    }
}
