<?php

declare(strict_types=1);

namespace Domain\Auth\Listeners;

use App\Core\Listeners\BaseListener;
use Domain\Auth\Events\NewDeviceDetectedEvent;
use Domain\Auth\Notifications\NewDeviceLoginNotification;

class NewDeviceDetectedListener extends BaseListener
{
    public function handle(NewDeviceDetectedEvent $event): void
    {
        $event->device->user->notify(new NewDeviceLoginNotification($event->device));
    }
}
