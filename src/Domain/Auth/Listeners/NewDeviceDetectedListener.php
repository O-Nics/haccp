<?php

declare(strict_types=1);

namespace Domain\Auth\Listeners;

use Domain\Auth\Events\NewDeviceDetectedEvent;

/**
 * Event dispatched when a suspicious login is detected
 * (new device type/platform not previously trusted)
 */
class NewDeviceDetectedListener
{
    public function __construct() {}

    public function handle(NewDeviceDetectedEvent $event): void
    {
        // Envoyer email ou notification à l'utilisateur
    }
}
