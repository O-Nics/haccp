<?php

declare(strict_types=1);

namespace App\Features\Auth\Data;

use App\Features\Device\Data\DeviceInfoData;
use Spatie\LaravelData\Data;

class CheckAuthData extends Data
{
    public function __construct(
        public bool $rememberMe,
        public DeviceInfoData $deviceInfo,
        public ?string $activeBusiness,
    ) {}
}
