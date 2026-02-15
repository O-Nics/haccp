<?php

namespace Domain\Business\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Shared\Models\BasePivot;

/**
 * @property-read Permission|null $permission
 * @property-read Role|null $role
 *
 * @mixin \Eloquent
 */
class RolePermission extends BasePivot
{
    use HasFactory;

    protected static function newFactory(): \Domain\Business\Database\Factories\RolePermissionFactory
    {
        return \Domain\Business\Database\Factories\RolePermissionFactory::new();
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
