<?php

namespace App\Features\Auth\Services;

use Laravel\Sanctum\NewAccessToken;

final readonly class TokenPair
{
    public function __construct(
        public NewAccessToken $access,
        public NewAccessToken $refresh,
    ) {}

    public function accessPlain(): string
    {
        return $this->access->plainTextToken;
    }

    public function accessExpiresAt(): int
    {
        return $this->access->accessToken->expires_at->getTimestamp();
    }

    public function refreshExpiresAt(): int
    {
        return $this->refresh->accessToken->expires_at->getTimestamp();
    }

    public function refreshPlain(): string
    {
        return $this->refresh->plainTextToken;
    }

    public function accessId(): int
    {
        return $this->access->accessToken->id;
    }

    public function refreshId(): int
    {
        return $this->refresh->accessToken->id;
    }
}
