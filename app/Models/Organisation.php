<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $owner
 *
 * @method static \Database\Factories\OrganisationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereSiret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organisation whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Organisation extends Model
{
    use HasFactory, HasUuids;

    /**
     * NOTE: This project does NOT use $fillable or $guarded.
     * Properties are assigned individually with explicit typing.
     * See CLAUDE.md for more details on this convention.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
