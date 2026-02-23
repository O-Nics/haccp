<?php

declare(strict_types=1);

namespace Domain\Notification\Data;

use Domain\Notification\Enums\NotificationChannel;
use Domain\Notification\Enums\NotificationSentStatus;
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
