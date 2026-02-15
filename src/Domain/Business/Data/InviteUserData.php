<?php

declare(strict_types=1);

namespace Domain\Business\Data;

use Spatie\LaravelData\Data;

class InviteUserData extends Data
{
    public function __construct(
        public string $email,
        public string $first_name,
        public string $last_name,
    ) {}
}
