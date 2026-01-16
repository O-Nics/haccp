<?php

declare(strict_types=1);

namespace App\Features\Auth\Listeners;

use App\Features\Auth\Events\NewDeviceDetectedEvent;
use App\Models\Device;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

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
