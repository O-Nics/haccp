<?php

// app/DataTransferObjects/DeviceInfoDTO.php

declare(strict_types=1);

namespace App\Features\Device\Data;

use App\Features\Device\Enums\DeviceType;
use App\Features\Device\Enums\Platform;
use Spatie\LaravelData\Data;

class DeviceInfoData extends Data
{
    public function __construct(
        public string $deviceId,
        public string $deviceName,
        public DeviceType $deviceType = DeviceType::UNKNOWN,
        public ?Platform $platform = null,
        public ?string $browser = null,
        public ?string $userAgent = null,
    ) {}
}
