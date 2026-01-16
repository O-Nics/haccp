<?php

namespace App\Features\Notifications\Data;

use App\Features\Notifications\Enums\NotificationChannel;
use App\Features\Notifications\Enums\NotificationSentStatus;
use Spatie\LaravelData\Data;

class StoreNotificationSentData extends Data
{
    public function __construct(
        public ?string $userId,
        public string $recipient,
        public NotificationChannel $type,
        public string $name,
        public ?string $subject,
        public ?string $contentHtml,
        public ?string $contentText,
        public NotificationSentStatus $status,
        public ?string $errorMessage,
    ) {}
}
