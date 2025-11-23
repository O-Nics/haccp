<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $restaurant_id
 * @property string $name
 * @property string $display_name
 * @property string|null $description
 * @property string $color
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\RestaurantRolePermission|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Restaurant $restaurant
 *
 * @method static \Database\Factories\RestaurantRoleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantRole onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantRole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantRole whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantRole whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantRole whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantRole whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantRole whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantRole whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantRole whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantRole withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantRole withoutTrashed()
 *
 * @mixin \Eloquent
 */
class RestaurantRole extends Model
{
    use HasFactory, SoftDeletes;

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'restaurant_role_permissions', 'restaurant_role_id', 'permission_id')
            ->using(RestaurantRolePermission::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function restaurantUsers(): HasMany
    {
        return $this->hasMany(RestaurantUser::class);
    }
}
