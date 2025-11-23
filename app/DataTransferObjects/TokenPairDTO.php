<?php

// app/DataTransferObjects/TokenPairDTO.php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;

class TokenPairDTO extends Data
{
    public function __construct(
        public string $accessToken,
        public int $accessTokenExpiresIn,
        public string $refreshToken,
        public int $refreshTokenExpiresIn,
        public string $deviceId,
        public string $tokenType = 'Bearer',
    ) {}

    /**
     * Format response for API
     */
    public function toApiResponse(): array
    {
        return [
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
            'device_id' => $this->deviceId,
        ];
    }
}
