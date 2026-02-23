<?php

declare(strict_types=1);

namespace Domain\Notification\Data;

use Domain\Notification\Enums\NotificationChannel;
use Domain\Notification\Enums\NotificationSentStatus;
use Spatie\LaravelData\Data;

class IndexNotificationSentRequestData extends Data
{
    public function __construct(
        public ?string $filterQuery = null,
        public ?NotificationSentStatus $status = null,
        public ?NotificationChannel $type = null,
        public string $sort = 'created_at',
        public string $order = 'desc',
        public int $perPage = 20,
        public int $page = 1,
    ) {}
}
