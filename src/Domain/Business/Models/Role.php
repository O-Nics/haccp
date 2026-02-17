<?php

namespace Domain\Business\Models;

use App\Core\Models\BaseModel;
use Domain\Business\Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Eloquent;

/**
 * @property int $id
 * @property int $business_id
 * @property int $is_system
 * @property string $name
 * @property string $display_name
 * @property string|null $description
 * @property string $color
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Business $business
 * @property-read Collection<int, Member> $members
 * @property-read int|null $members_count
 * @property-read RolePermission|null $pivot
 * @property-read Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 *
 * @method static Builder<static>|Role betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static RoleFactory factory($count = null, $state = [])
 * @method static Builder<static>|Role filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|Role forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static Builder<static>|Role newModelQuery()
 * @method static Builder<static>|Role newQuery()
 * @method static Builder<static>|Role onlyTrashed()
 * @method static Builder<static>|Role order(?string $sort, ?string $order)
 * @method static Builder<static>|Role query()
 * @method static Builder<static>|Role whereBusinessId($value)
 * @method static Builder<static>|Role whereColor($value)
 * @method static Builder<static>|Role whereCreatedAt($value)
 * @method static Builder<static>|Role whereDeletedAt($value)
 * @method static Builder<static>|Role whereDescription($value)
 * @method static Builder<static>|Role whereDisplayName($value)
 * @method static Builder<static>|Role whereId($value)
 * @method static Builder<static>|Role whereIsSystem($value)
 * @method static Builder<static>|Role whereName($value)
 * @method static Builder<static>|Role whereUpdatedAt($value)@
 * @method static Builder<static>|Role withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Role withoutTrashed()
 *
 * @mixin Eloquent
 */
class Role extends BaseModel
{
    use HasFactory, HasUuids, SoftDeletes;

    protected static function newFactory(): RoleFactory
    {
        return RoleFactory::new();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id')
            ->using(RolePermission::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}
