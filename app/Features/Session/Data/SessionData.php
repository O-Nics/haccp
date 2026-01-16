<?php

// app/DataTransferObjects/DeviceInfoDTO.php

declare(strict_types=1);

namespace App\Features\Session\Data;

use App\Features\Auth\Data\TokenData;
use App\Models\Session;
use Spatie\LaravelData\Data;

class SessionData extends Data
{
    public function __construct(
        public Session $session,
        public TokenData $accessToken,
        public TokenData $refreshToken,
    ) {}
}
