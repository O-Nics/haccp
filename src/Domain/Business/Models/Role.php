<?php

namespace Domain\Business\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Member> $members
 * @property-read int|null $members_count
 * @property-read RolePermission|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
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

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}
