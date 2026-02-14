<?php

declare(strict_types=1);

namespace Domain\Auth\Data;

use Spatie\LaravelData\Data;

class StoreUserData extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $password,
    ) {}
}
