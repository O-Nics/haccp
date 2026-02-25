<?php

declare(strict_types=1);

namespace Domain\Business\Data;

use Spatie\LaravelData\Data;

class StoreRoleData extends Data
{
    public function __construct(
        public string $displayName,
        public ?string $description,
        public array $permissionIds
    ) {}
}
