<?php

declare(strict_types=1);

namespace App\Features\Business\Resources;

use App\Core\Resources\BaseJsonResource;
use App\Features\BusinessRole\Resources\BusinessRoleResource;
use App\Models\Business;
use Illuminate\Http\Request;

/**
 * @mixin Business
 */
class BusinessResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->pivot->status,
            'role' => $this->pivot != null ? new BusinessRoleResource($this->pivot->role) : null,
        ];
    }
}
