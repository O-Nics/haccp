<?php

declare(strict_types=1);

namespace Domain\Business\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateRoleData extends Data
{
    public function __construct(
        public string|Optional $name,
        public string|Optional $displayName,
        public string|Optional|null $description,
        public array|Optional $permissionIds
    ) {}
}
