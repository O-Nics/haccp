<?php

namespace App\Features\Notifications\Enums;


use App\Core\Enums\Enumerations;
use App\Features\Notifications\NotificationChannel\NotificationChannelState;

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
