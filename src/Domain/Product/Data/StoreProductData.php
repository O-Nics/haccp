<?php

declare(strict_types=1);

namespace Domain\Product\Data;

use Spatie\LaravelData\Data;

class StoreProductData extends Data
{
    public function __construct(
        public string $name,
    ) {}
}
