<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Password;

use Spatie\LaravelData\Data;

class ForgotPasswordData extends Data
{
    public function __construct(
        public string $email,
    ) {}
}
