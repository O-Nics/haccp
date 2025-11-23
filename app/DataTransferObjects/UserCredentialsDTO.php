<?php

// app/DataTransferObjects/UserCredentialsDTO.php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;

class UserCredentialsDTO extends Data
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}
}
