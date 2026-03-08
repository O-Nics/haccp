<?php

declare(strict_types=1);

namespace Domain\Product\Data;

use Spatie\LaravelData\Data;

class DeleteProductData extends Data
{
    public function __construct(
        public array $productIds,
    ) {}
}
