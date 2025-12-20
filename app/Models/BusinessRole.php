<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $business_id
 * @property string $name
 * @property string $display_name
 * @property string|null $description
 * @property string $color
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Business $business
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BusinessUser> $businessUsers
 * @property-read int|null $business_users_count
 * @property-read \App\Models\BusinessRolePermission|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 *
 * @method static \Database\Factories\BusinessRoleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessRole onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessRole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessRole whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessRole whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessRole whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessRole whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessRole whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessRole whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessRole whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessRole withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BusinessRole withoutTrashed()
 *
 * @mixin \Eloquent
 */
class BusinessRole extends Model
{
    use HasFactory, SoftDeletes;

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'business_role_permissions', 'business_role_id', 'permission_id')
            ->using(BusinessRolePermission::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function businessUsers(): HasMany
    {
        return $this->hasMany(BusinessUser::class);
    }
}
