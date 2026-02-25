<?php

declare(strict_types=1);

namespace Domain\Auth\Models;

use App\Core\Models\BaseAuthenticable;
use Domain\Auth\Database\Factories\UserFactory;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Domain\Business\Models\Organisation;
use Domain\Notification\Models\NotificationSent;
use Eloquent;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
 * @property int $temporary_password
 * @property string|null $pin_code
 * @property string $local
 * @property int $notify_email
 * @property int $notify_sms
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $last_login_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Business> $businesses
 * @property-read int|null $businesses_count
 * @property-read Collection<int, Device> $devices
 * @property-read int|null $devices_count
 * @property-read string $full_name
 * @property-read Collection<int, Member> $memberships
 * @property-read int|null $memberships_count
 * @property-read Collection<int, NotificationSent> $notificationSents
 * @property-read int|null $notification_sents_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, Organisation> $organizations
 * @property-read int|null $organizations_count
 * @property-read Collection<int, Session> $sessions
 * @property-read int|null $sessions_count
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static Builder<static>|User betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static UserFactory factory($count = null, $state = [])
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
 * @method static Builder<static>|User whereTemporaryPassword($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|User withoutTrashed()
 *
 * @mixin Eloquent
 */
class User extends BaseAuthenticable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable, SoftDeletes;

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

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

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function getFullNameAttribute(): string
    {
        if (empty($this->first_name) && empty($this->last_name)) {
            return trim($this->email);
        }

        return trim("{$this->first_name} {$this->last_name}");
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class, 'members', 'user_id', 'business_id');
    }

    public function memberOf(Business $business): ?Member
    {
        /** @var Member|null $member */
        $member = $this->memberships()
            ->where('business_id', $business->id)
            ->first();

        $member?->load('role.permissions');

        return $member;
    }

    public function checkPassword(string $password): bool
    {
        return Hash::check($password, $this->password);
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organisation::class, 'owner_id', 'id');
    }

    public function notificationSents(): HasMany
    {
        return $this->hasMany(NotificationSent::class);
    }

    public function currentSession(): ?Session
    {
        $token = $this->currentAccessToken();

        if (! $token) {
            return null;
        }

        if ($token->expires_at && $token->expires_at->isPast()) {
            return null;
        }

        /** @var Session|null */
        return $this->sessions()
            ->where(function ($query) use ($token) {
                $query->where('access_token_id', $token->id)
                    ->orWhere('refresh_token_id', $token->id);
            })
            ->first();

    }
}
