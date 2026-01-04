<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\DataTransferObjects\AuthData;
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
//                    'token' => 'falseToken',
                    'token' => $this->accessToken,
                    'type' => $this->tokenType,
                    'expires_in' => $this->accessTokenExpiresIn,
                ],
                'refresh' => [
                    'token' => $this->refreshToken,
//                    'token' => "dz",
                    'expires_in' => $this->refreshTokenExpiresIn,
                ],
            ],
            'active_business' => $this->activeBusiness ? new BusinessResource($this->activeBusiness) : null,
        ];
    }
}
