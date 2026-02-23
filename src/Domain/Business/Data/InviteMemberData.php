<?php

declare(strict_types=1);

namespace Domain\Business\Data;

use Spatie\LaravelData\Data;

class InviteMemberData extends Data
{
    public function __construct(
        public string $email,
        public string $firstName,
        public string $lastName,
        public string $roleId,
    ) {}
}
