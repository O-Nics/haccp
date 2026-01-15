<?php

namespace App\Features\Notifications\NotificationChannel;

use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Mail;

class NotificationChannelEmail extends NotificationChannelState
{
    public function resend(NotificationSent $notificationSent): void
    {
        Mail::html($notificationSent->content, function ($message) use ($notificationSent) {
            $message->to($notificationSent->recipient)
                ->subject($notificationSent->subject);
        });
    }
}
