<?php

namespace App\Features\BusinessRole\Data;

use Spatie\LaravelData\Data;

class IndexBusinessRoleData extends Data
{
    public function __construct(
        public ?string $filterQuery,
        public ?string $sort,
        public ?string $order,
        public int $perPage,
        public int $page,
    ) {}

}
