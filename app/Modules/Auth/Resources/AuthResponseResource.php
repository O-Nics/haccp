<?php

declare(strict_types=1);

namespace App\Modules\Auth\Resources;

use App\Core\Resources\BaseJsonResource;
use App\Modules\Business\Resources\BusinessResource;
use App\Modules\User\Resources\UserResource;
use Domain\Auth\Data\AuthData;
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
