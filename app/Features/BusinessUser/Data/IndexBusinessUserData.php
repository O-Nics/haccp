<?php

declare(strict_types=1);

namespace App\Features\BusinessUser\Data;

use Spatie\LaravelData\Data;

class IndexBusinessUserData extends Data
{
    public function __construct(
        public ?string $filterQuery,
        public ?string $sort,
        public ?string $order,
        public int $perPage,
        public int $page,
    ) {}
}
