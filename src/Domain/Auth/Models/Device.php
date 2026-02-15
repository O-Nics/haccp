<?php

declare(strict_types=1);

namespace Domain\Auth\Models;

use App\Core\Models\BaseModel;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $user_id
 * @property string $device_id
 * @property string $platform
 * @property string $ip_address
 * @property string|null $model
 * @property bool $remember_me
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Domain\Auth\Models\Session|null $session
 * @property-read \Domain\Auth\Models\User $user
 *
 * @method static Builder<static>|Device betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static \Database\Factories\DeviceFactory factory($count = null, $state = [])
 * @method static Builder<static>|Device filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|Device forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static Builder<static>|Device newModelQuery()
 * @method static Builder<static>|Device newQuery()
 * @method static Builder<static>|Device order(?string $sort, ?string $order)
 * @method static Builder<static>|Device query()
 * @method static Builder<static>|Device whereCreatedAt($value)
 * @method static Builder<static>|Device whereDeviceId($value)
 * @method static Builder<static>|Device whereId($value)
 * @method static Builder<static>|Device whereIpAddress($value)
 * @method static Builder<static>|Device whereModel($value)
 * @method static Builder<static>|Device wherePlatform($value)
 * @method static Builder<static>|Device whereRememberMe($value)
 * @method static Builder<static>|Device whereUpdatedAt($value)
 * @method static Builder<static>|Device whereUserId($value)
 *
 * @mixin Eloquent
 */
class Device extends BaseModel
{
    use HasFactory;

    protected static function newFactory(): \Domain\Auth\Database\Factories\DeviceFactory
    {
        return \Domain\Auth\Database\Factories\DeviceFactory::new();
    }

    protected function casts(): array
    {
        return [
            'remember_me' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function session(): HasOne
    {
        return $this->hasOne(Session::class);
    }
}
