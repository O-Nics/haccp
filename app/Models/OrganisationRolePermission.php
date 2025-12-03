<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property-read \App\Models\OrganisationRole|null $organisationRole
 * @property-read \App\Models\Permission|null $permission
 *
 * @method static \Database\Factories\OrganisationRolePermissionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganisationRolePermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganisationRolePermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganisationRolePermission query()
 *
 * @mixin \Eloquent
 */
class OrganisationRolePermission extends Pivot
{
    use HasFactory;

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    public function organisationRole(): BelongsTo
    {
        return $this->belongsTo(OrganisationRole::class);
    }
}
