<?php

namespace Domain\Business\Models;

use Domain\Auth\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Shared\Models\BaseModel;

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
 * @property-read User $owner
 *
 * @mixin \Eloquent
 */
class Organisation extends BaseModel
{
    use HasFactory, HasUuids;

    protected static function newFactory(): \Domain\Business\Database\Factories\OrganisationFactory
    {
        return \Domain\Business\Database\Factories\OrganisationFactory::new();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
