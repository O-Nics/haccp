<?php

declare(strict_types=1);

namespace Domain\Auth\Listeners;

use App\Core\Listeners\BaseListener;
use Domain\Auth\Events\PasswordResetCompletedEvent;
use Domain\Auth\Notifications\PasswordResetConfirmedNotification;

class SendPasswordResetConfirmedNotificationListener extends BaseListener
{
    public function handle(PasswordResetCompletedEvent $event): void
    {
        $event->user->notify(new PasswordResetConfirmedNotification);
    }
}
