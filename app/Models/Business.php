<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BusinessRole> $businessRoles
 * @property-read int|null $business_roles_count
 * @property-read \App\Models\BusinessType $businessType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BusinessUser> $businessUsers
 * @property-read int|null $business_users_count
 * @property-read \App\Models\Organisation $organisation
 * @property-read \App\Models\BusinessUser|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\BusinessFactory factory($count = null, $state = [])
 * @method static Builder<static>|Business newModelQuery()
 * @method static Builder<static>|Business newQuery()
 * @method static Builder<static>|Business onlyTrashed()
 * @method static Builder<static>|Business query()
 * @method static Builder<static>|Business whereAddress($value)
 * @method static Builder<static>|Business whereBusinessTypeId($value)
 * @method static Builder<static>|Business whereCity($value)
 * @method static Builder<static>|Business whereCountry($value)
 * @method static Builder<static>|Business whereCreatedAt($value)
 * @method static Builder<static>|Business whereDeletedAt($value)
 * @method static Builder<static>|Business whereEmail($value)
 * @method static Builder<static>|Business whereId($value)
 * @method static Builder<static>|Business whereName($value)
 * @method static Builder<static>|Business whereOrganisationId($value)
 * @method static Builder<static>|Business wherePhone($value)
 * @method static Builder<static>|Business wherePostalCode($value)
 * @method static Builder<static>|Business whereTimezone($value)
 * @method static Builder<static>|Business whereUpdatedAt($value)
 * @method static Builder<static>|Business withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|Business withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Business extends Model
{
    use HasFactory, SoftDeletes;

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
        return $this->hasMany(BusinessRole::class);
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'business_users')
            ->using(BusinessUser::class)
            ->withPivot(['id', 'role_id', 'business_role_id', 'status', 'invited_at', 'joined_at', 'invited_by'])
            ->withTimestamps();
    }
}
