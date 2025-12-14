<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $business_id
 * @property string $user_id
 * @property string|null $invited_by
 * @property int|null $role_id
 * @property int|null $business_role_id
 * @property Carbon|null $invited_at
 * @property Carbon|null $joined_at
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\Business $business
 * @property-read \App\Models\User|null $invitedBy
 * @property-read \App\Models\BusinessRole|null $businessRole
 * @property-read \App\Models\Role|null $role
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereInvitedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereInvitedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereJoinedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereBusinessRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessUser whereUserId($value)
 *
 * @mixin \Eloquent
 */
class BusinessUser extends Model
{
    use HasFactory;

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
