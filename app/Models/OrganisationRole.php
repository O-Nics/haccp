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
 * @property string $organisation_id
 * @property string $name
 * @property string $display_name
 * @property string|null $description
 * @property string $color
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BusinessUser> $businessUsers
 * @property-read int|null $business_users_count
 * @property-read \App\Models\Organisation $organisation
 * @property-read \App\Models\OrganisationRolePermission|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 *
 * @method static \Database\Factories\OrganisationRoleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganisationRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganisationRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganisationRole onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganisationRole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganisationRole whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganisationRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganisationRole whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganisationRole whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganisationRole whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganisationRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganisationRole whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganisationRole whereOrganisationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganisationRole whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganisationRole withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganisationRole withoutTrashed()
 *
 * @mixin \Eloquent
 */
class OrganisationRole extends Model
{
    use HasFactory, SoftDeletes;

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'organisation_role_permissions', 'organisation_role_id', 'permission_id')
            ->using(OrganisationRolePermission::class);
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function businessUsers(): HasMany
    {
        return $this->hasMany(BusinessUser::class);
    }
}
