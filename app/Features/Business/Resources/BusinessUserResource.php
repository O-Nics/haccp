<?php

declare(strict_types=1);

namespace App\Features\Business\Resources;

use App\Core\Resources\BaseJsonResource;
use App\Features\BusinessRole\Resources\BusinessRoleResource;
use App\Features\User\Resources\UserResource;
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
            'role' => $this->pivot != null ? new BusinessRoleResource($this->pivot->businessRole ?? $this->pivot->role) : null,

        ];
    }
}
