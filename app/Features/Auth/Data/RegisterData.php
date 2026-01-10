<?php

declare(strict_types=1);

namespace App\Features\Auth\Data;

use App\Features\Device\Data\DeviceInfoData;
use Spatie\LaravelData\Data;

class RegisterData extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $password,
        public bool $rememberMe,
        public DeviceInfoData $deviceInfo,

    ) {}
}
