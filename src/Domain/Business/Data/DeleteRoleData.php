<?php

declare(strict_types=1);

namespace Domain\Business\Data;

use Spatie\LaravelData\Data;

class DeleteRoleData extends Data
{
    public function __construct(
        public array $roleIds
    ) {}

    public function toDto(): DeleteRoleData
    {
        return new DeleteRoleData(
            roleIds: $this->roleIds,
        );
    }
}
