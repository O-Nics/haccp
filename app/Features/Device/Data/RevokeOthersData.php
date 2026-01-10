<?php

declare(strict_types=1);

namespace App\Features\Device\Data;

use Spatie\LaravelData\Data;

class RevokeOthersData extends Data
{
    public function __construct(
        public string $currentDeviceId,
    ) {}
}
