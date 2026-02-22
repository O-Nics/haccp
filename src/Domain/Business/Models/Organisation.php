<?php

declare(strict_types=1);

namespace Domain\Business\Models;

use App\Core\Models\BaseModel;
use Domain\Auth\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Eloquent;
use Domain\Business\Database\Factories\OrganisationFactory;

/**
 * @property string $id
 * @property string $owner_id
 * @property string $name
 * @property string|null $siret
 * @property string|null $address
 * @property string|null $postal_code
 * @property string|null $city
 * @property string|null $country
 * @property string|null $phone
 * @property string|null $email
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $owner
 *
 * @method static Builder<static>|Organisation betweenDates($column, ?string $startAt = null, ?string $endAt = null, $boolean = 'and', $not = false)
 * @method static OrganisationFactory factory($count = null, $state = [])
 * @method static Builder<static>|Organisation filtered(?string $filterQuery = null, array $searchableColumns = [])
 * @method static Builder<static>|Organisation forPeriod(?string $startAt = null, ?string $endAt = null, ?string $columnStart = 'start_at', ?string $columnEnd = 'end_at')
 * @method static Builder<static>|Organisation newModelQuery()
 * @method static Builder<static>|Organisation newQuery()
 * @method static Builder<static>|Organisation order(?string $sort, ?string $order)
 * @method static Builder<static>|Organisation query()
 * @method static Builder<static>|Organisation whereAddress($value)
 * @method static Builder<static>|Organisation whereCity($value)
 * @method static Builder<static>|Organisation whereCountry($value)
 * @method static Builder<static>|Organisation whereCreatedAt($value)
 * @method static Builder<static>|Organisation whereEmail($value)
 * @method static Builder<static>|Organisation whereId($value)
 * @method static Builder<static>|Organisation whereName($value)
 * @method static Builder<static>|Organisation whereOwnerId($value)
 * @method static Builder<static>|Organisation wherePhone($value)
 * @method static Builder<static>|Organisation wherePostalCode($value)
 * @method static Builder<static>|Organisation whereSiret($value)
 * @method static Builder<static>|Organisation whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Organisation extends BaseModel
{
    use HasFactory, HasUuids, SoftDeletes;

    protected static function newFactory(): OrganisationFactory
    {
        return OrganisationFactory::new();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
