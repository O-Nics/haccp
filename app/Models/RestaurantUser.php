<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $restaurant_id
 * @property string $user_id
 * @property string|null $invited_by
 * @property int|null $role_id
 * @property int|null $restaurant_role_id
 * @property string $status
 * @property Carbon|null $invited_at
 * @property Carbon|null $joined_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Restaurant $restaurant
 * @property-read User $user
 * @property-read User|null $invitedBy
 * @property-read Role|null $role
 * @property-read RestaurantRole|null $restaurantRole
 *
 * @method static \Database\Factories\RestaurantUserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantUser whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantUser whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantUser whereInvitedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantUser whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantUser whereRestaurantRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantUser whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantUser whereInvitedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantUser whereJoinedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantUser whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class RestaurantUser extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'invited_at' => 'datetime',
            'joined_at' => 'datetime',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function restaurantRole(): BelongsTo
    {
        return $this->belongsTo(RestaurantRole::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
