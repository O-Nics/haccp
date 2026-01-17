<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Core\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @property int $id
 * @property int $device_id
 * @property string $user_id
 * @property int $access_token_id
 * @property int $refresh_token_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read PersonalAccessToken $accessToken
 * @property-read \App\Models\Device $device
 * @property-read PersonalAccessToken $refreshToken
 * @property-read \App\Models\User $user
 *
 * @method static Builder<static>|Session betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static Builder<static>|Session filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|Session forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session newQuery()
 * @method static Builder<static>|Session order(?string $sort, ?string $order)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereAccessTokenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereRefreshTokenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Session whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Session extends BaseModel
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function accessToken(): BelongsTo
    {
        return $this->belongsTo(PersonalAccessToken::class, 'access_token_id');
    }

    public function refreshToken(): BelongsTo
    {
        return $this->belongsTo(PersonalAccessToken::class, 'refresh_token_id');
    }
}
