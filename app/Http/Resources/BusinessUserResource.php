<?php

namespace App\Http\Resources;

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
            'devices' => DeviceResource::collection($this->whenLoaded('devices')),
            'businesses' => BusinessUserResource::collection($this->whenLoaded('businessUsers'))

        ];
    }
}
