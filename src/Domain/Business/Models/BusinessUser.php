<?php

namespace Domain\Business\Models;

use App\Core\Models\BasePivot;
use Domain\Auth\Models\User;
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
 * @property-read Business $business
 * @property-read Role|null $businessRole
 * @property-read User|null $invitedBy
 * @property-read Role|null $role
 * @property-read User $user
 *
 * @mixin \Eloquent
 */
class BusinessUser extends BasePivot
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'business_users';

    public $incrementing = true;

    protected static function newFactory(): \Domain\Business\Database\Factories\BusinessUserFactory
    {
        return \Domain\Business\Database\Factories\BusinessUserFactory::new();
    }

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
        return $this->belongsTo(Role::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
