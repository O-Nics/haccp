<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\RolePermission|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 *
 * @method static \Database\Factories\RoleFactory factory($count = null, $state = [])
 * @method static Builder<static>|Role newModelQuery()
 * @method static Builder<static>|Role newQuery()
 * @method static Builder<static>|Role query()
 * @method static Builder<static>|Role whereCreatedAt($value)
 * @method static Builder<static>|Role whereDescription($value)
 * @method static Builder<static>|Role whereDisplayName($value)
 * @method static Builder<static>|Role whereId($value)
 * @method static Builder<static>|Role whereName($value)
 * @method static Builder<static>|Role whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Role extends Model
{
    use HasFactory;

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id')
            ->using(RolePermission::class);
    }

    public function restaurantUsers(): HasMany
    {
        return $this->hasMany(RestaurantUser::class);
    }
}
