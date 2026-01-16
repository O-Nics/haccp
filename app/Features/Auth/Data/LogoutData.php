<?php

declare(strict_types=1);

namespace App\Features\Auth\Data;

use Spatie\LaravelData\Data;

class LogoutData extends Data
{
    public function __construct(
        public string $deviceId,
    ) {}
}
