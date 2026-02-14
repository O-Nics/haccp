<?php

declare(strict_types=1);

namespace Domain\Auth\Data;

use Spatie\LaravelData\Data;

class CheckAuthData extends Data
{
    public function __construct(
        public DeviceData $deviceInfo,
        public ?string $activeBusinessId,
    ) {}
}
