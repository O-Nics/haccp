<?php

declare(strict_types=1);

namespace App\Features\Device\Resources;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Device
 */
class DeviceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'device_id' => $this->device_id,
            'device_name' => $this->device_name,
            'device_type' => $this->device_type,
            'platform' => $this->platform,
            'ip_address' => $this->ip_address,
            'last_used_at' => $this->last_used_at,
            'token_expires_at' => $this->token_expires_at,
            'is_token_expired' => $this->isTokenExpired(),
        ];
    }
}
