<?php

declare(strict_types=1);

namespace App\Features\User\Resources;

use App\Core\Resources\BaseJsonResource;
use App\Features\Business\Requests\BusinessResource;
use App\Features\Session\Resources\DeviceResource;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @mixin User
 */
class BusinessUserResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this),
            'invited_by' => new BusinessUserResource($this->whenLoaded('invitedBy')),
            'invited_at' => $this->pivot?->invited_at,
            'joined_at' => $this->pivot?->joined_at,
            'status' => $this->pivot?->status,
            'role' => $this->pivot != null ? new RoleResource($this->pivot->businessRole ?? $this->pivot->role) : null,

        ];
    }
}
