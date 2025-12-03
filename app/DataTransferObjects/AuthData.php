<?php

// app/DataTransferObjects/TokenPairDTO.php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Models\User;
use Spatie\LaravelData\Data;

class AuthData extends Data
{
    public function __construct(
        public string $accessToken,
        public int $accessTokenExpiresIn,
        public string $refreshToken,
        public int $refreshTokenExpiresIn,
        public string $deviceId,
        public string $tokenType = 'Bearer',
    ) {}
}
