<?php

declare(strict_types=1);

namespace Domain\Business\Data;

use Domain\Business\Models\Member;
use Spatie\LaravelData\Data;

class MemberInvitedData extends Data
{
    public function __construct(
        public readonly Member $member,
        public readonly ?string $temporaryPassword,
    ) {}
}