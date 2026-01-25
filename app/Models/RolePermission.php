<?php

namespace App\Models;

use App\Models\Core\BasePivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read \App\Models\Permission|null $permission
 * @property-read \App\Models\Role|null $role
 *
 * @method static Builder<static>|RolePermission betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static \Database\Factories\RolePermissionFactory factory($count = null, $state = [])
 * @method static Builder<static>|RolePermission filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|RolePermission forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolePermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolePermission newQuery()
 * @method static Builder<static>|RolePermission order(?string $sort, ?string $order)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolePermission query()
 *
 * @mixin \Eloquent
 */
class RolePermission extends BasePivot
{
    use HasFactory;

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
