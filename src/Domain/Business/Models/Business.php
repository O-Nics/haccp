<?php

namespace Domain\Business\Models;

use Domain\Auth\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Shared\Models\BaseModel;

/**
 * @property int $id
 * @property int $business_type_id
 * @property string $name
 * @property string $organisation_id
 * @property string $address
 * @property string $postal_code
 * @property string $city
 * @property string $timezone
 * @property string $country
 * @property string|null $phone
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Role> $businessRoles
 * @property-read int|null $business_roles_count
 * @property-read BusinessType $businessType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, BusinessUser> $businessUsers
 * @property-read int|null $business_users_count
 * @property-read Organisation $organisation
 * @property-read BusinessUser|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static Builder<static>|Business betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static \Database\Factories\BusinessFactory factory($count = null, $state = [])
 * @method static Builder<static>|Business filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|Business forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static Builder<static>|Business newModelQuery()
 * @method static Builder<static>|Business newQuery()
 * @method static Builder<static>|Business onlyTrashed()
 * @method static Builder<static>|Business order(?string $sort, ?string $order)
 * @method static Builder<static>|Business query()
 *
 * @mixin \Eloquent
 */
class Business extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): \Domain\Business\Database\Factories\BusinessFactory
    {
        return \Domain\Business\Database\Factories\BusinessFactory::new();
    }

    public function businessType(): BelongsTo
    {
        return $this->belongsTo(BusinessType::class);
    }

    public function businessUsers(): HasMany
    {
        return $this->hasMany(BusinessUser::class);
    }

    public function businessRoles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'business_users')
            ->using(BusinessUser::class)
            ->withPivot(['id', 'role_id', 'status', 'invited_at', 'joined_at', 'invited_by'])
            ->withTimestamps();
    }
}
