<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\DataTransferObjects\AuthData;
use App\Models\User;
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
                    'expires_in' => $this->refreshTokenExpiresIn,
                ],
            ],
        ];
    }
}
