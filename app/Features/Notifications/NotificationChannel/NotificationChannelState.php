<?php

namespace App\Features\Notifications\NotificationChannel;


use Illuminate\Notifications\Events\NotificationSent;

abstract class NotificationChannelState
{
    abstract public function resend(NotificationSent $notificationSent): void;
}
