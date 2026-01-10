<?php

namespace App\Features\Business\Requests;

use App\Http\Resources\RoleResource;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Business
 */
class BusinessResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->pivot->status,
            'roles' => $this->whenLoaded('businessRoles'),
            'role' => $this->pivot != null ? new RoleResource($this->pivot->businessRole ?? $this->pivot->role) : null,
        ];
    }
}
