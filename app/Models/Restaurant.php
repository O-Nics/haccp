<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $siret
 * @property string $address
 * @property string $postal_code
 * @property string $city
 * @property string $country
 * @property string|null $phone
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\RestaurantType|null $restaurantType
 *
 * @method static \Database\Factories\RestaurantFactory factory($count = null, $state = [])
 * @method static Builder<static>|Restaurant newModelQuery()
 * @method static Builder<static>|Restaurant newQuery()
 * @method static Builder<static>|Restaurant onlyTrashed()
 * @method static Builder<static>|Restaurant query()
 * @method static Builder<static>|Restaurant whereAddress($value)
 * @method static Builder<static>|Restaurant whereCity($value)
 * @method static Builder<static>|Restaurant whereCountry($value)
 * @method static Builder<static>|Restaurant whereCreatedAt($value)
 * @method static Builder<static>|Restaurant whereDeletedAt($value)
 * @method static Builder<static>|Restaurant whereEmail($value)
 * @method static Builder<static>|Restaurant whereId($value)
 * @method static Builder<static>|Restaurant whereName($value)
 * @method static Builder<static>|Restaurant wherePhone($value)
 * @method static Builder<static>|Restaurant wherePostalCode($value)
 * @method static Builder<static>|Restaurant whereSiret($value)
 * @method static Builder<static>|Restaurant whereUpdatedAt($value)
 * @method static Builder<static>|Restaurant withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Restaurant withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Restaurant extends Model
{
    use HasFactory, SoftDeletes;

    public function restaurantType(): BelongsTo
    {
        return $this->belongsTo(RestaurantType::class);
    }

    public function restaurantRoles(): HasMany
    {
        return $this->hasMany(RestaurantRole::class);
    }

    public function restaurantUsers(): HasMany
    {
        return $this->hasMany(RestaurantUser::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'restaurant_users')
            ->withPivot(['role_id', 'restaurant_role_id', 'status', 'invited_at', 'joined_at', 'invited_by'])
            ->withTimestamps();
    }
}
