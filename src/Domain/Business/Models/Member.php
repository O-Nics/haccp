<?php

declare(strict_types=1);

namespace Domain\Business\Models;

use App\Core\Models\BaseModel;
use Domain\Auth\Models\User;
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
 * @property-read Business $business
 * @property-read Role|null $role
 * @property-read User $user
 * @property-read User|null $invitedBy
 *
 * @mixin \Eloquent
 */
class Member extends BaseModel
{
    use HasFactory;

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
