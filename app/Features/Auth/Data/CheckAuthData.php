<?php

declare(strict_types=1);

namespace App\Features\Auth\Data;

use Spatie\LaravelData\Data;

class CheckAuthData extends Data
{
    public function __construct(
        public DeviceData $deviceInfo,
        public ?string $activeBusinessId,
    ) {}
}
