<?php

namespace App\Features\Notifications\NotificationChannel;


use App\Models\NotificationSent;

abstract class NotificationChannelState
{
    abstract public function resend(NotificationSent $notificationSent): void;
}
