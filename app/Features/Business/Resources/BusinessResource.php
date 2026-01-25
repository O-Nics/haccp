<?php

declare(strict_types=1);

namespace App\Features\Business\Resources;

use App\Core\Resources\BaseJsonResource;
use App\Features\User\Resources\RoleResource;
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
            'role' => $this->pivot != null ? new RoleResource($this->pivot->role) : null,
        ];
    }
}
