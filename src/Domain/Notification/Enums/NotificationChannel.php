<?php

namespace Domain\Notification\Enums;

use App\Core\Enums\Enumerations;
use Domain\Notification\Channels\NotificationChannelEmail;
use Domain\Notification\Channels\NotificationChannelState;

enum NotificationChannel: string
{
    use Enumerations;

    case Sms = 'sms';
    case InApp = 'in_app';
    case Email = 'email';

    public function model(): NotificationChannelState
    {
        return match ($this) {
            self::Email => new NotificationChannelEmail,
        };
    }
}
