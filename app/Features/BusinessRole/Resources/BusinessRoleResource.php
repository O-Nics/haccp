<?php

declare(strict_types=1);

namespace App\Features\BusinessRole\Resources;

use App\Core\Resources\BaseJsonResource;
use App\Features\Permission\Resources\PermissionResource;
use App\Features\User\Resources\UserResource;
use App\Models\Role;
use Illuminate\Http\Request;

/**
 * @mixin Role
 */
class BusinessRoleResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'description' => $this->description,

            // Relation
            'users' => $this->whenLoaded('users', function () {
                return UserResource::collection($this->users);
            }),
            'permissions' => $this->whenLoaded('permissions', function () {
                return PermissionResource::collection($this->permissions);
            }),
        ];
    }
}
