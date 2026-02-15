<?php

declare(strict_types=1);

namespace App\Modules\App\Resources;

use App\Core\Resources\BaseJsonResource;
use App\Modules\Business\Resources\PermissionResource;
use Illuminate\Http\Request;

class LoadInitialDataResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'permissions' => PermissionResource::collection($this->resource),
        ];
    }
}
