<?php

declare(strict_types=1);

namespace App\Modules\Business\Resources;

use App\Core\Resources\BaseJsonResource;
use App\Modules\User\Resources\UserResource;
use Domain\Business\Models\Role;
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
