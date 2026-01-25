<?php

namespace App\Models;

use App\Models\Core\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property int $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Business> $businesses
 * @property-read int|null $businesses_count
 *
 * @method static Builder<static>|BusinessType betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static \Database\Factories\BusinessTypeFactory factory($count = null, $state = [])
 * @method static Builder<static>|BusinessType filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|BusinessType forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessType newQuery()
 * @method static Builder<static>|BusinessType order(?string $sort, ?string $order)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessType whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessType whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class BusinessType extends BaseModel
{
    use HasFactory;

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }
}
