<?php

declare(strict_types=1);

namespace Domain\Business\Models;

use App\Core\Models\BasePivot;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read Member|null $member
 * @property-read Role|null $role
 *
 * @method static Builder<static>|MemberRole betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static Builder<static>|MemberRole filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|MemberRole forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static Builder<static>|MemberRole newModelQuery()
 * @method static Builder<static>|MemberRole newQuery()
 * @method static Builder<static>|MemberRole order(?string $sort, ?string $order)
 * @method static Builder<static>|MemberRole query()
 *
 * @mixin Eloquent
 */
class MemberRole extends BasePivot
{
    protected $table = 'member_role';

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
