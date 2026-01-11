<?php

declare(strict_types=1);

namespace App\Features\Auth\Resources;

use App\Core\Resources\BaseJsonResource;
use App\Features\Auth\Data\AuthData;
use App\Features\Business\Requests\BusinessResource;
use App\Features\User\Resources\UserResource;
use Illuminate\Http\Request;

/**
 * @mixin AuthData
 */
class AuthResponseResource extends BaseJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this->user),
            'tokens' => [
                'access' => new TokenResponse($this->accessToken),
                'refresh' => new TokenResponse($this->refreshToken),
            ],
            'active_business' => $this->activeBusiness ? new BusinessResource($this->activeBusiness) : null,
        ];
    }
}
