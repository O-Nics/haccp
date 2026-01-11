<?php

declare(strict_types=1);

namespace App\Features\Auth\Resources;

use App\Features\Auth\Data\AuthData;
use App\Features\Auth\Data\TokenData;
use App\Features\Business\Requests\BusinessResource;
use App\Features\User\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TokenData
 */
class TokenResponse extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'token' => $this->token,
            'expires_at' => $this->expiresAt,
            'token_type' => $this->tokenType,
        ];
    }
}
