<?php

declare(strict_types=1);

namespace Domain\Auth\Data;

use App\Models\Business;
use Domain\Auth\Models\User;
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
