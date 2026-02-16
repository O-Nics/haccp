<?php

declare(strict_types=1);

namespace Domain\Business\Models;

use App\Core\Models\BaseModel;
use Domain\Auth\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $business_id
 * @property string $user_id
 * @property string|null $invited_by
 * @property int|null $role_id
 * @property \Illuminate\Support\Carbon|null $invited_at
 * @property \Illuminate\Support\Carbon|null $joined_at
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Domain\Business\Models\Business $business
 * @property-read User|null $invitedBy
 * @property-read \Domain\Business\Models\Role|null $role
 * @property-read User $user
 *
 * @method static Builder<static>|Member betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static \Domain\Business\Database\Factories\MemberFactory factory($count = null, $state = [])
 * @method static Builder<static>|Member filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|Member forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member newQuery()
 * @method static Builder<static>|Member order(?string $sort, ?string $order)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereInvitedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereInvitedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereJoinedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Member extends BaseModel
{
    use HasFactory, HasUuids;

    protected $table = 'members';

    protected static function newFactory(): \Domain\Business\Database\Factories\MemberFactory
    {
        return \Domain\Business\Database\Factories\MemberFactory::new();
    }

    protected $with = ['role', 'invitedBy'];

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

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
