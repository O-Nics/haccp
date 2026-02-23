<?php

declare(strict_types=1);

namespace Domain\Auth\Data;

use Spatie\LaravelData\Data;

class RegisterData extends Data
{
    public function __construct(
        public StoreUserData $storeUserData,
        public DeviceData $deviceInfo,

    ) {}
}
