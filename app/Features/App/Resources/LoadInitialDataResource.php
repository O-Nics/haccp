<?php

declare(strict_types=1);

namespace App\Features\App\Resources;

use App\Core\Resources\BaseJsonResource;
use App\Core\Resources\PermissionResource;
use App\Features\App\Data\LoadInitialDataData;
use App\Features\User\Resources\UserResource;
use App\Models\Role;
use Illuminate\Http\Request;

/**
 * @mixin LoadInitialDataData
 */
class LoadInitialDataResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'permissions' => PermissionResource::collection($this->permissions),
        ];
    }
}
