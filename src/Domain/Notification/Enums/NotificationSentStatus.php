<?php

declare(strict_types=1);

namespace Domain\Notification\Enums;

enum NotificationSentStatus: string
{
    case Sent = 'sent';
    case Error = 'error';
    case Blocked = 'blocked';

    case Skipped = 'skipped';
}
