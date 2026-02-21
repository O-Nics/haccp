<?php

declare(strict_types=1);

namespace Domain\Auth\ValueObjects;

final readonly class AccessToken
{
    public function __construct(
        private string $plainToken,
        private int $tokenId,
        private int $expiresAt,
    ) {}

    public function plainToken(): string
    {
        return $this->plainToken;
    }

    public function tokenId(): int
    {
        return $this->tokenId;
    }

    public function expiresAt(): int
    {
        return $this->expiresAt;
    }
}