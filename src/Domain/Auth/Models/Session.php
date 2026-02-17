<?php

declare(strict_types=1);

namespace Domain\Auth\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Eloquent;
use Domain\Auth\Database\Factories\SessionFactory;

/**
 * @property int $id
 * @property int $device_id
 * @property string $user_id
 * @property int $access_token_id
 * @property int $refresh_token_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PersonalAccessToken $accessToken
 * @property-read Device $device
 * @property-read PersonalAccessToken $refreshToken
 * @property-read User $user
 *
 * @method static Builder<static>|Session betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static SessionFactory factory($count = null, $state = [])
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
 * @method static Builder<static>|Session whereRefreshTokenId($value)
 * @method static Builder<static>|Session whereUpdatedAt($value)
 * @method static Builder<static>|Session whereUserId($value)
 *
 * @mixin Eloquent
 */
class Session extends BaseModel
{
    use HasFactory, HasUuids, SoftDeletes;

    protected static function newFactory(): SessionFactory
    {
        return SessionFactory::new();
    }

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
