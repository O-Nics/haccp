<?php

declare(strict_types=1);

namespace App\Features\Auth\Data;

use Spatie\LaravelData\Data;

class RefreshTokenData extends Data
{
    public function __construct(
        public string $refreshToken,
        public string $deviceId,
    ) {}
}
