<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DeviceType;
use App\Enums\Platform;
use App\Models\Base\BaseModel;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $user_id
 * @property string $device_id
 * @property string $device_name
 * @property DeviceType $device_type
 * @property Platform|null $platform
 * @property string|null $browser
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string $refresh_token_hash
 * @property Carbon|null $token_expires_at
 * @property Carbon|null $last_used_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\User $user
 *
 * @method static Builder<static>|Device betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static \Database\Factories\DeviceFactory factory($count = null, $state = [])
 * @method static Builder<static>|Device filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|Device forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static Builder<static>|Device newModelQuery()
 * @method static Builder<static>|Device newQuery()
 * @method static Builder<static>|Device order(?string $sort, ?string $order)
 * @method static Builder<static>|Device query()
 * @method static Builder<static>|Device whereBrowser($value)
 * @method static Builder<static>|Device whereCreatedAt($value)
 * @method static Builder<static>|Device whereDeviceId($value)
 * @method static Builder<static>|Device whereDeviceName($value)
 * @method static Builder<static>|Device whereDeviceType($value)
 * @method static Builder<static>|Device whereId($value)
 * @method static Builder<static>|Device whereIpAddress($value)
 * @method static Builder<static>|Device whereLastUsedAt($value)
 * @method static Builder<static>|Device wherePlatform($value)
 * @method static Builder<static>|Device whereRefreshTokenHash($value)
 * @method static Builder<static>|Device whereTokenExpiresAt($value)
 * @method static Builder<static>|Device whereUpdatedAt($value)
 * @method static Builder<static>|Device whereUserAgent($value)
 * @method static Builder<static>|Device whereUserId($value)
 *
 * @mixin Eloquent
 */
class Device extends BaseModel
{
    use HasFactory;

    protected $hidden = [
        'refresh_token_hash',
    ];

    protected function casts(): array
    {
        return [
            'device_type' => DeviceType::class,
            'platform' => Platform::class,
            'last_used_at' => 'datetime',
            'token_expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isTokenExpired(): bool
    {
        return $this->token_expires_at?->isPast() ?? false;
    }

    public static function generateRefreshToken(): string
    {
        return Str::random(64);
    }

    public function setRefreshToken(string $token): void
    {
        $this->refresh_token_hash = hash('sha256', $token);
    }

    public function verifyRefreshToken(string $token): bool
    {
        return hash_equals(
            $this->refresh_token_hash,
            hash('sha256', $token)
        );
    }

    public function touchLastUsed(): void
    {
        $this->last_used_at = Carbon::now();
    }
}
