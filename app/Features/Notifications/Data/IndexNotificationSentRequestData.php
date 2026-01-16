<?php

namespace App\Features\Notifications\Data;

use App\Features\Notifications\Enums\NotificationChannel;
use App\Features\Notifications\Enums\NotificationSentStatus;
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
