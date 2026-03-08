<?php

declare(strict_types=1);

namespace Domain\Product\Models;

use App\Core\Models\BaseModel;
use Domain\Business\Models\Business;
use Domain\Product\Database\Factories\ProductFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $name
 * @property string $business_id
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Business $business
 *
 * @method static Builder<static>|Product betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static ProductFactory factory($count = null, $state = [])
 * @method static Builder<static>|Product filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|Product forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static Builder<static>|Product newModelQuery()
 * @method static Builder<static>|Product newQuery()
 * @method static Builder<static>|Product onlyTrashed()
 * @method static Builder<static>|Product order(?string $sort, ?string $order)
 * @method static Builder<static>|Product query()
 * @method static Builder<static>|Product whereBusinessId($value)
 * @method static Builder<static>|Product whereCreatedAt($value)
 * @method static Builder<static>|Product whereDeletedAt($value)
 * @method static Builder<static>|Product whereId($value)
 * @method static Builder<static>|Product whereName($value)
 * @method static Builder<static>|Product whereUpdatedAt($value)
 * @method static Builder<static>|Product withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Product withoutTrashed()
 *
 * @mixin Eloquent
 */
class Product extends BaseModel
{
    use HasFactory, HasUuids, SoftDeletes;

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
