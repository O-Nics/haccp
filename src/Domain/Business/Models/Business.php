<?php

namespace Domain\Business\Models;

use App\Core\Models\BaseModel;
use Domain\Business\Database\Factories\BusinessFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Business\Models\Role> $businessRoles
 * @property-read int|null $business_roles_count
 * @property-read \Domain\Business\Models\BusinessType $businessType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Domain\Business\Models\Member> $members
 * @property-read int|null $members_count
 * @property-read \Domain\Business\Models\Organisation $organisation
 *
 * @method static Builder<static>|Business betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static \Domain\Business\Database\Factories\BusinessFactory factory($count = null, $state = [])
 * @method static Builder<static>|Business filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|Business forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static Builder<static>|Business newModelQuery()
 * @method static Builder<static>|Business newQuery()
 * @method static Builder<static>|Business onlyTrashed()
 * @method static Builder<static>|Business order(?string $sort, ?string $order)
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
class Business extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): BusinessFactory
    {
        return BusinessFactory::new();
    }

    public function businessType(): BelongsTo
    {
        return $this->belongsTo(BusinessType::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function businessRoles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }
}
