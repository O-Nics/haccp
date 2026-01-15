<?php

namespace App\Features\Notifications\Enums;

enum NotificationSentStatus: string
{
    case Sent = 'sent';
    case Error = 'error';
    case Blocked = 'blocked';
}
