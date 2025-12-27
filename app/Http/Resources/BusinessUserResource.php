<?php

namespace App\Http\Resources;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Business
 */
class BusinessUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->pivot->status,
            //            'role' => new RoleResource($this->pivot->businessRole ?? $this->pivot->role),
            //            'invited_by' => new UserResource($this->pivot->invitedBy),
            //            'invited_at' => $this->pivot->invited_at,
            //            'joined_at' => $this->pivot->joined_at,

        ];
    }
}
