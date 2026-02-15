<?php

declare(strict_types=1);

namespace Domain\Auth\Events;

use Domain\Auth\Models\Device;
use Domain\Auth\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewDeviceDetectedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $user,
        public Device $device,
    ) {}
}
