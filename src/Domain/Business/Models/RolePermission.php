<?php

namespace Domain\Business\Models;

use App\Core\Models\BasePivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Eloquent;
use Domain\Business\Database\Factories\RolePermissionFactory;

/**
 * @property-read Permission|null $permission
 * @property-read Role|null $role
 *
 * @method static Builder<static>|RolePermission betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static RolePermissionFactory factory($count = null, $state = [])
 * @method static Builder<static>|RolePermission filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|RolePermission forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static Builder<static>|RolePermission newModelQuery()
 * @method static Builder<static>|RolePermission newQuery()
 * @method static Builder<static>|RolePermission order(?string $sort, ?string $order)
 * @method static Builder<static>|RolePermission query()
 *
 * @mixin Eloquent
 */
class RolePermission extends BasePivot
{
    use HasFactory, HasUuids;

    protected static function newFactory(): RolePermissionFactory
    {
        return RolePermissionFactory::new();
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
