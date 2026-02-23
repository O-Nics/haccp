<?php

declare(strict_types=1);

namespace Domain\Auth\Data;

use Spatie\LaravelData\Data;

class LoginData extends Data
{
    public function __construct(
        public string $email,
        public string $password,
        public DeviceData $deviceData,
        public ?string $activeBusiness,
    ) {}
}
