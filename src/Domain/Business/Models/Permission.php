<?php

declare(strict_types=1);

namespace Domain\Business\Models;

use App\Core\Models\BaseModel;
use Domain\Business\Database\Factories\PermissionFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $can
 * @property string $category
 * @property string $category_label
 * @property string $display_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read RolePermission|null $pivot
 * @property-read Collection<int, Role> $businessRoles
 * @property-read int|null $business_roles_count
 * @property-read Collection<int, Role> $roles
 * @property-read int|null $roles_count
 *
 * @method static Builder<static>|Permission betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static PermissionFactory factory($count = null, $state = [])
 * @method static Builder<static>|Permission filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|Permission forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static Builder<static>|Permission newModelQuery()
 * @method static Builder<static>|Permission newQuery()
 * @method static Builder<static>|Permission order(?string $sort, ?string $order)
 * @method static Builder<static>|Permission query()
 * @method static Builder<static>|Permission whereCan($value)
 * @method static Builder<static>|Permission whereCategory($value)
 * @method static Builder<static>|Permission whereCategoryLabel($value)
 * @method static Builder<static>|Permission whereCreatedAt($value)
 * @method static Builder<static>|Permission whereDisplayName($value)
 * @method static Builder<static>|Permission whereId($value)
 * @method static Builder<static>|Permission whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Permission extends BaseModel
{
    use HasFactory, HasUuids;

    protected static function newFactory(): PermissionFactory
    {
        return PermissionFactory::new();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id')
            ->using(RolePermission::class);
    }
}
