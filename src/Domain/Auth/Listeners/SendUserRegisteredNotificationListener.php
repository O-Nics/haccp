<?php

declare(strict_types=1);

namespace Domain\Auth\Listeners;

use App\Core\Listeners\BaseListener;
use Domain\Auth\Events\UserRegisteredEvent;
use Domain\Auth\Notifications\UserRegisterNotification;

class SendUserRegisteredNotificationListener extends BaseListener
{
    public function handle(UserRegisteredEvent $event): void
    {
        $event->user->notify(new UserRegisterNotification);
    }
}
