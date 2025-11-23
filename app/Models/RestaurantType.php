<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property int $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Restaurant> $restaurants
 * @property-read int|null $restaurants_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantType whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RestaurantType whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class RestaurantType extends Model
{
    use HasFactory;

    public function restaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class);
    }
}
