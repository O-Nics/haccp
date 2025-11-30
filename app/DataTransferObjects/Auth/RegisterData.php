<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Auth;

use App\DataTransferObjects\DeviceInfoData;
use Spatie\LaravelData\Data;

class RegisterData extends Data
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $email,
        public string $password,
        public bool $remember_me,
        public DeviceInfoData $deviceInfo,

    ) {}
}
