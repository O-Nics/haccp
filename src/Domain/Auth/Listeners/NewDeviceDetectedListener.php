<?php

declare(strict_types=1);

namespace Domain\Auth\Listeners;

use App\Core\Listeners\BaseListener;
use Domain\Auth\Events\NewDeviceDetectedEvent;

/**
 * Event dispatched when a suspicious login is detected
 * (new device type/platform not previously trusted)
 */
class NewDeviceDetectedListener extends BaseListener
{
    public function handle(NewDeviceDetectedEvent $event): void
    {
        // Envoyer email ou notification à l'utilisateur
    }
}
