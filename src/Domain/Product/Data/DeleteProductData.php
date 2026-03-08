<?php

declare(strict_types=1);

namespace Domain\Product\Data;

use Domain\Notification\Enums\NotificationChannel;
use Domain\Notification\Enums\NotificationSentStatus;
use Spatie\LaravelData\Data;

class DeleteProductData extends Data
{
    public function __construct(
        public array $productIds,
    ) {}
}
