<?php

declare(strict_types=1);

namespace Domain\Business\Data;

use Domain\Business\Enums\MemberStatusEnum;
use Spatie\LaravelData\Data;

class StoreMemberData extends Data
{
    public function __construct(
        public string $businessId,
        public string $userId,
        public string $invitedBy,
        public MemberStatusEnum $status,
        public array $roleIds,
    ) {}
}
