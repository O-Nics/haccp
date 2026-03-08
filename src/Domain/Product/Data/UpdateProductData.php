<?php

declare(strict_types=1);

namespace Domain\Product\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateProductData extends Data
{
    public function __construct(
        public Optional|string $name,
    ) {}
}
