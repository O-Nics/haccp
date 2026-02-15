<?php

namespace Domain\Business\Models;

use Domain\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Shared\Models\BaseModel;

/**
 * @property int $id
 * @property int $business_id
 * @property int $is_system
 * @property string $name
 * @property string $display_name
 * @property string|null $description
 * @property string $color
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Business $business
 * @property-read \Illuminate\Database\Eloquent\Collection<int, BusinessUser> $businessUsers
 * @property-read int|null $business_users_count
 * @property-read RolePermission|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @mixin \Eloquent
 */
class Role extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): \Domain\Business\Database\Factories\RoleFactory
    {
        return \Domain\Business\Database\Factories\RoleFactory::new();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id')
            ->using(RolePermission::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function businessUsers(): HasMany
    {
        return $this->hasMany(BusinessUser::class);
    }

    // les users ayant ce role via businessUsers
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'business_users', 'role_id', 'user_id');
    }
}
