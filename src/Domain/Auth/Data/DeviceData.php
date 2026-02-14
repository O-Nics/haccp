<?php

declare(strict_types=1);

namespace Domain\Auth\Data;

use Spatie\LaravelData\Data;

class DeviceData extends Data
{
    public function __construct(
        public string $deviceId,
        public string $devicePlatform,
        public ?string $deviceModel,
        public ?bool $rememberMe,
        public string $ip
    ) {}
}
