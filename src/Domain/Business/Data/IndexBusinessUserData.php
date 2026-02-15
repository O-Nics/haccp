<?php

declare(strict_types=1);

namespace Domain\Business\Data;

use Spatie\LaravelData\Data;

class IndexBusinessUserData extends Data
{
    public function __construct(
        public string $businessId,
        public ?string $filterQuery,
        public ?string $sort,
        public ?string $order,
        public int $perPage,
        public int $page,
    ) {}
}
