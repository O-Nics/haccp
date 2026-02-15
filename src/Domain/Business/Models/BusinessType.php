<?php

namespace Domain\Business\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property int $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Business> $businesses
 * @property-read int|null $businesses_count
 *
 * @mixin \Eloquent
 */
class BusinessType extends BaseModel
{
    use HasFactory;

    protected static function newFactory(): \Domain\Business\Database\Factories\BusinessTypeFactory
    {
        return \Domain\Business\Database\Factories\BusinessTypeFactory::new();
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }
}
