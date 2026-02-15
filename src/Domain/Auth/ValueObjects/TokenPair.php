<?php

declare(strict_types=1);

namespace Domain\Auth\ValueObjects;

final readonly class TokenPair
{
    public function __construct(
        private string $accessToken,
        private int $accessTokenId,
        private int $accessExpiresAt,
        private string $refreshToken,
        private int $refreshTokenId,
        private int $refreshExpiresAt,
    ) {}

    public function accessPlain(): string
    {
        return $this->accessToken;
    }

    public function accessId(): int
    {
        return $this->accessTokenId;
    }

    public function accessExpiresAt(): int
    {
        return $this->accessExpiresAt;
    }

    public function refreshPlain(): string
    {
        return $this->refreshToken;
    }

    public function refreshId(): int
    {
        return $this->refreshTokenId;
    }

    public function refreshExpiresAt(): int
    {
        return $this->refreshExpiresAt;
    }
}
