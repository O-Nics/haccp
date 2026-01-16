<?php

declare(strict_types=1);

namespace App\Features\Auth\Events;

use App\Models\Device;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Event dispatched when a suspicious login is detected
 * (new device type/platform not previously trusted)
 */
class NewDeviceDetectedEvent
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        public readonly User $user,
        public readonly Device $device,
    ) {}
}
