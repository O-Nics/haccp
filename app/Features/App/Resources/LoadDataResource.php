<?php

declare(strict_types=1);

namespace App\Features\App\Resources;

use App\Core\Resources\BaseJsonResource;
use App\Features\App\Data\LoadDataData;
use App\Features\Permission\Resources\PermissionResource;
use App\Features\User\Resources\UserResource;
use App\Models\Role;
use Illuminate\Http\Request;

/**
 * @mixin LoadDataData
 */
class LoadDataResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'permissions' => PermissionResource::collection($this->permissions),
        ];
    }
}
