<?php

namespace Domain\Business\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Domain\Business\Database\Factories\BusinessTypeFactory;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Eloquent;

/**
 * @property int $id
 * @property string $name
 * @property int $code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Business> $businesses
 * @property-read int|null $businesses_count
 *
 * @method static Builder<static>|BusinessType betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static BusinessTypeFactory factory($count = null, $state = [])
 * @method static Builder<static>|BusinessType filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|BusinessType forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static Builder<static>|BusinessType newModelQuery()
 * @method static Builder<static>|BusinessType newQuery()
 * @method static Builder<static>|BusinessType order(?string $sort, ?string $order)
 * @method static Builder<static>|BusinessType query()
 * @method static Builder<static>|BusinessType whereCode($value)
 * @method static Builder<static>|BusinessType whereCreatedAt($value)
 * @method static Builder<static>|BusinessType whereId($value)
 * @method static Builder<static>|BusinessType whereName($value)
 * @method static Builder<static>|BusinessType whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class BusinessType extends BaseModel
{
    use HasFactory, HasUuids;

    protected static function newFactory(): BusinessTypeFactory
    {
        return BusinessTypeFactory::new();
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }
}
