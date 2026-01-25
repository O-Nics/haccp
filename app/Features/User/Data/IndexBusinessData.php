<?php

// app/DataTransferObjects/TokenPairDTO.php

declare(strict_types=1);

namespace App\Features\User\Data;

use Spatie\LaravelData\Data;

class IndexBusinessData extends Data
{
    public function __construct(
        public ?string $filterQuery,
        public ?string $sort,
        public ?string $order,
        public int $perPage,
        public int $page,
    ) {}
}
