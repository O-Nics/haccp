<?php

declare(strict_types=1);

namespace Domain\Auth\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $email
 * @property string $token
 * @property Carbon $expires_at
 * @property Carbon|null $confirmed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|PasswordResetToken betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static Builder<static>|PasswordResetToken filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|PasswordResetToken forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static Builder<static>|PasswordResetToken newModelQuery()
 * @method static Builder<static>|PasswordResetToken newQuery()
 * @method static Builder<static>|PasswordResetToken order(?string $sort, ?string $order)
 * @method static Builder<static>|PasswordResetToken query()
 * @method static Builder<static>|PasswordResetToken whereConfirmedAt($value)
 * @method static Builder<static>|PasswordResetToken whereCreatedAt($value)
 * @method static Builder<static>|PasswordResetToken whereEmail($value)
 * @method static Builder<static>|PasswordResetToken whereExpiresAt($value)
 * @method static Builder<static>|PasswordResetToken whereId($value)
 * @method static Builder<static>|PasswordResetToken whereToken($value)
 * @method static Builder<static>|PasswordResetToken whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class PasswordResetToken extends BaseModel
{
    protected $hidden = [
        'token',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'confirmed_at' => 'datetime',
        ];
    }

    public static function generateToken(): string
    {
        return Str::random(64);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed_at !== null;
    }

    public function markAsConfirmed(): void
    {
        $this->confirmed_at = now();
    }

    public function verifyToken(string $token): bool
    {
        return hash_equals($this->token, $token);
    }
}
