<?php

namespace App\Models;

use App\Models\Core\BasePivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $business_id
 * @property string $user_id
 * @property string|null $invited_by
 * @property int|null $role_id
 * @property int|null $business_role_id
 * @property \Illuminate\Support\Carbon|null $invited_at
 * @property \Illuminate\Support\Carbon|null $joined_at
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Business $business
 * @property-read \App\Models\BusinessRole|null $businessRole
 * @property-read \App\Models\User|null $invitedBy
 * @property-read \App\Models\Role|null $role
 * @property-read \App\Models\User $user
 *
 * @method static Builder<static>|BusinessUser betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static Builder<static>|BusinessUser filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|BusinessUser forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser newQuery()
 * @method static Builder<static>|BusinessUser order(?string $sort, ?string $order)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereBusinessRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereInvitedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereInvitedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereJoinedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereUserId($value)
 *
 * @mixin \Eloquent
 */
class BusinessUser extends BasePivot
{
    protected $table = 'business_users';

    public $incrementing = true;

    /**
     * Relations to eager load on every query.
     */
    protected $with = ['businessRole', 'role', 'invitedBy'];

    protected function casts(): array
    {
        return [
            'invited_at' => 'datetime',
            'joined_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function businessRole(): BelongsTo
    {
        return $this->belongsTo(BusinessRole::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
