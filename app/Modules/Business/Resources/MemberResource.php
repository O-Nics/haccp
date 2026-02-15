<?php

declare(strict_types=1);

namespace App\Modules\Business\Resources;

use App\Core\Resources\BaseJsonResource;
use App\Modules\User\Resources\UserResource;
use Domain\Business\Models\Member;
use Illuminate\Http\Request;

/**
 * @mixin Member
 */
class MemberResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'invited_by' => new UserResource($this->whenLoaded('invitedBy')),
            'invited_at' => $this->invited_at,
            'joined_at' => $this->joined_at,
            'status' => $this->status,
            'role' => new BusinessRoleResource($this->whenLoaded('role')),
        ];
    }
}
