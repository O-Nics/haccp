<?php

namespace App\Http\Resources;

use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin BusinessUser
 */
class BusinessUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,


            // Relations
            'role'=>$this->businessRole,
            'devices' => DeviceResource::collection($this->whenLoaded('devices')),
            'business' => $this->whenLoaded('business'),

        ];
    }
}
