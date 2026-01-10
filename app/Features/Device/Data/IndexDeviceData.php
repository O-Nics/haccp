<?php

declare(strict_types=1);

namespace App\Features\Device\Data;

use Spatie\LaravelData\Data;

class IndexDeviceData extends Data
{
    public function __construct(
        public ?string $filterQuery,
        public ?string $sort,
        public ?string $order,
        public int $perPage,
        public int $page,
    ) {}
}
