<?php

// app/DataTransferObjects/TokenPairDTO.php

declare(strict_types=1);

namespace App\Features\Auth\Data;

use App\Models\Business;
use App\Models\User;
use Spatie\LaravelData\Data;

class AuthData extends Data
{
    public function __construct(
        public User $user,
        public TokenData $accessToken,
        public TokenData $refreshToken,
        public ?Business $activeBusiness = null,
    ) {}
}
