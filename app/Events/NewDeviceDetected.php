<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Device;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when a suspicious login is detected
 * (new device type/platform not previously trusted)
 */
class NewDeviceDetected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly Device $device,
    ) {}
}
