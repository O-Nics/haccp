<?php

declare(strict_types=1);

namespace Domain\Business\Data;

use Spatie\LaravelData\Data;

class DeleteMemberData extends Data
{
    public function __construct(
        public array $memberIds,
    ) {}
}
