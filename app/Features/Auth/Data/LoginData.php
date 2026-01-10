<?php

declare(strict_types=1);

namespace App\Features\Auth\Data;

use App\Features\Device\Data\DeviceInfoData;
use Spatie\LaravelData\Data;

class LoginData extends Data
{
    public function __construct(
        public string $email,
        public string $password,
        public bool $rememberMe,
        public DeviceInfoData $deviceInfo,
        public ?string $activeBusiness,
    ) {}
}
