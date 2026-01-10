<?php

declare(strict_types=1);

namespace App\Features\Auth\Data;

use Spatie\LaravelData\Data;

class ResetPasswordData extends Data
{
    public function __construct(
        public string $email,
        public string $token,
        public string $password,
    ) {}
}
