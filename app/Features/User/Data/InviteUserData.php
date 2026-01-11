<?php

// app/DataTransferObjects/TokenPairDTO.php

declare(strict_types=1);

namespace App\Features\User\Data;

use App\Features\Auth\Data\TokenData;
use App\Models\Business;
use App\Models\User;
use Spatie\LaravelData\Data;

class InviteUserData extends Data
{
    public function __construct(
        public string $email,
        public string $first_name,
        public string $last_name,
    ) {}
}
