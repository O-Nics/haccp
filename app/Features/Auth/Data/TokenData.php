<?php

// app/DataTransferObjects/TokenPairDTO.php

declare(strict_types=1);

namespace App\Features\Auth\Data;

use App\Models\Business;
use App\Models\User;
use Spatie\LaravelData\Data;

class TokenData extends Data
{
    public function __construct(
        public string $token,
        public int    $expiresAt,
        public string $tokenType,
    ) {}
}
