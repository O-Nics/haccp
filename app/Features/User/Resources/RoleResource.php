<?php

declare(strict_types=1);

namespace App\Features\User\Resources;

use App\Core\Resources\BaseJsonResource;
use App\Models\Role;
use Illuminate\Http\Request;

/**
 * @mixin Role
 */
class RoleResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'description' => $this->description,
            'permissions' => $this->permissions,
        ];
    }
}
