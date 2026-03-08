<?php

declare(strict_types=1);

namespace Domain\Business\Models;

use App\Core\Models\BasePivot;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $role_id
 * @property string $member_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Member $member
 * @property-read Role $role
 *
 * @method static Builder<static>|MemberRole betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static Builder<static>|MemberRole filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|MemberRole forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static Builder<static>|MemberRole newModelQuery()
 * @method static Builder<static>|MemberRole newQuery()
 * @method static Builder<static>|MemberRole order(?string $sort, ?string $order)
 * @method static Builder<static>|MemberRole query()
 * @method static Builder<static>|MemberRole whereCreatedAt($value)
 * @method static Builder<static>|MemberRole whereId($value)
 * @method static Builder<static>|MemberRole whereMemberId($value)
 * @method static Builder<static>|MemberRole whereRoleId($value)
 * @method static Builder<static>|MemberRole whereUpdatedAt($value)
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
