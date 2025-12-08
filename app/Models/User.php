<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Base\BaseAuthenticable;
use Eloquent;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @property string $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string|null $pin_code
 * @property string $local
 * @property string $timezone
 * @property int $notify_email
 * @property int $notify_sms
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $last_login_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, \App\Models\BusinessUser> $businessUserInvitations
 * @property-read int|null $business_user_invitations_count
 * @property-read Collection<int, \App\Models\BusinessUser> $businessUsers
 * @property-read int|null $business_users_count
 * @property-read Collection<int, \App\Models\Device> $devices
 * @property-read int|null $devices_count
 * @property-read string $full_name
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, \App\Models\Organisation> $organizations
 * @property-read int|null $organizations_count
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static Builder<static>|User betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|User forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User onlyTrashed()
 * @method static Builder<static>|User order(?string $sort, ?string $order)
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereDeletedAt($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereFirstName($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereLastLoginAt($value)
 * @method static Builder<static>|User whereLastName($value)
 * @method static Builder<static>|User whereLocal($value)
 * @method static Builder<static>|User whereNotifyEmail($value)
 * @method static Builder<static>|User whereNotifySms($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User wherePinCode($value)
 * @method static Builder<static>|User whereTimezone($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|User withoutTrashed()
 *
 * @mixin Eloquent
 */
class User extends BaseAuthenticable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable, SoftDeletes;

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function getFullNameAttribute(): string
    {
        if (empty($this->first_name) && empty($this->last_name)) {
            return trim($this->email);
        }

        return trim("{$this->first_name} {$this->last_name}");
    }

    public function businessUsers(): HasMany
    {
        return $this->hasMany(BusinessUser::class, 'user_id', 'id');
    }

    public function businessUserInvitations(): HasMany
    {
        return $this->hasMany(BusinessUser::class, 'invited_by', 'id');
    }

    public function checkPassword(string $password): bool
    {
        return Hash::check($password, $this->password);
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organisation::class, 'owner_id', 'id');
    }
}
