<?php

declare(strict_types=1);

namespace App\Features\Business\Data;

use Spatie\LaravelData\Data;

class RegisterUserData extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $password,
    ) {}
}
