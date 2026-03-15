<?php

declare(strict_types=1);

namespace App\Modules\Business\Resources;

use App\Core\Resources\BaseJsonResource;
use Domain\Business\Models\Role;
use Illuminate\Http\Request;

/**
 * @mixin Role
 */
class RoleResource extends BaseJsonResource
{
    /**
     * Transforms a Role model into an API response array.
     * Relations (members, permissions) are included only when explicitly eager-loaded.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'description' => $this->description,
            'is_system' => $this->is_system,
            // Relation
            'members' => $this->whenLoaded('members', function () {
                return MemberResource::collection($this->members);
            }),
            'permissions' => $this->whenLoaded('permissions', function () {
                return PermissionResource::collection($this->permissions);
            }),
        ];
    }
}
