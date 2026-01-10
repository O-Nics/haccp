<?php

declare(strict_types=1);

namespace App\Features\Auth\Resources;

use App\Features\Auth\Data\AuthData;
use App\Features\Business\Requests\BusinessResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AuthData
 */
class AuthResponse extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this->user),
            'tokens' => [
                'access' => [
                    'token' => $this->accessToken,
                    'type' => $this->tokenType,
                    'expires_in' => $this->accessTokenExpiresIn,
                ],
                'refresh' => [
                    'token' => $this->refreshToken,
                   'type' => $this->tokenType,
                    'expires_in' => $this->refreshTokenExpiresIn,
                ],
            ],
            'active_business' => $this->activeBusiness ? new BusinessResource($this->activeBusiness) : null,
        ];
    }
}
