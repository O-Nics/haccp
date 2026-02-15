<?php

declare(strict_types=1);

namespace Domain\Auth\Listeners;

use App\Core\Listeners\BaseListener;
use Domain\Auth\Events\PasswordResetRequestedEvent;
use Domain\Auth\Notifications\PasswordResetNotification;

class SendPasswordResetNotificationListener extends BaseListener
{
    public function handle(PasswordResetRequestedEvent $event): void
    {
        $event->user->notify(new PasswordResetNotification($event->token));
    }
}
