<?php

declare(strict_types=1);

namespace Domain\Notification\Notifications;

use Domain\Auth\Models\User;
use Illuminate\Notifications\Notification;

class BaseNotification extends Notification
{
    public function mustSend(User $notifiable): bool
    {
        return true;
    }

    public function isConfidential(): bool
    {
        return false;
    }
}
