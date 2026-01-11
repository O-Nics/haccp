<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Core\BaseModel;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @property int $id
 * @property string $user_id
 * @property int $device_id
 * @property int|null $access_token_id
 * @property string $refresh_token_hash
 * @property Carbon $refresh_token_expires_at
 * @property Carbon|null $last_used_at
 * @property Carbon|null $revoked_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read Device $device
 * @property-read PersonalAccessToken|null $accessToken
 *
 * @method static Builder<static>|Session betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static Builder<static>|Session filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|Session forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static Builder<static>|Session newModelQuery()
 * @method static Builder<static>|Session newQuery()
 * @method static Builder<static>|Session order(?string $sort, ?string $order)
 * @method static Builder<static>|Session query()
 * @method static Builder<static>|Session whereAccessTokenId($value)
 * @method static Builder<static>|Session whereCreatedAt($value)
 * @method static Builder<static>|Session whereDeviceId($value)
 * @method static Builder<static>|Session whereId($value)
 * @method static Builder<static>|Session whereLastUsedAt($value)
 * @method static Builder<static>|Session whereRefreshTokenExpiresAt($value)
 * @method static Builder<static>|Session whereRefreshTokenHash($value)
 * @method static Builder<static>|Session whereRevokedAt($value)
 * @method static Builder<static>|Session whereUpdatedAt($value)
 * @method static Builder<static>|Session whereUserId($value)
 *
 * @mixin Eloquent
 */
class Session extends BaseModel
{
    protected $table = 'auth_sessions';

    protected $hidden = [
        'refresh_token_hash',
    ];

    protected function casts(): array
    {
        return [
            'refresh_token_expires_at' => 'datetime',
            'last_used_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the session
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the device associated with the session
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Get the access token associated with the session
     */
    public function accessToken(): BelongsTo
    {
        return $this->belongsTo(PersonalAccessToken::class, 'access_token_id');
    }

    /**
     * Generate a new random refresh token (64 characters)
     */
    public static function generateRefreshToken(): string
    {
        return Str::random(96);
    }

    /**
     * Hash and set the refresh token (SHA-256)
     */
    public function setRefreshToken(string $token): void
    {
        $this->refresh_token_hash = Hash::make($token);
    }

    /**
     * Verify the provided refresh token matches the stored hash (constant-time comparison)
     */
    public function verifyRefreshToken(string $token): bool
    {
        return Hash::check($token, $this->refresh_token_hash);
    }

    /**
     * Check if the refresh token is expired
     */
    public function isTokenExpired(): bool
    {
        return $this->refresh_token_expires_at->isPast();
    }

    /**
     * Check if the session is revoked
     */
    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    /**
     * Revoke the session by setting revoked_at timestamp
     */
    public function revoke(): void
    {
        $this->revoked_at = now();
        $this->save();
    }

    /**
     * Update the last_used_at timestamp
     */
    public function touchLastUsed(): void
    {
        $this->last_used_at = now();
    }

    /**
     * Scope: Query active sessions (not revoked, not expired)
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('revoked_at')
            ->where('refresh_token_expires_at', '>', now());
    }
}
