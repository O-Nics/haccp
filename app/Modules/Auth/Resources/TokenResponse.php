<?php

declare(strict_types=1);

namespace App\Modules\Auth\Resources;

use Domain\Auth\Data\TokenData;
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
        ];
    }
}
