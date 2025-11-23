<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Auth;

use Spatie\LaravelData\Data;

class LogoutData extends Data
{
    public function __construct(
        public string $deviceId,
    ) {}
}
