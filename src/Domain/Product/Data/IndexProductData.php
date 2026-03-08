<?php

declare(strict_types=1);

namespace Domain\Product\Data;

use Domain\Notification\Enums\NotificationChannel;
use Domain\Notification\Enums\NotificationSentStatus;
use Spatie\LaravelData\Data;

class IndexProductData extends Data
{
    public function __construct(
        public ?string $filterQuery,
        public ?string $sort,
        public ?string $order,
        public int $perPage,
        public int $page,
    ) {}
}
