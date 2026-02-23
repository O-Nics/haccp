<?php

declare(strict_types=1);

namespace App\Core\Notifications;

use Domain\Auth\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

abstract class BaseNotification extends Notification
{
    use Queueable;

    public function mustSend(User $notifiable): bool
    {
        return true;
    }

    public function isConfidential(): bool
    {
        return false;
    }
}
