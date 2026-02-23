<?php

declare(strict_types=1);

namespace Domain\Auth\Events;

use App\Core\Events\BaseEvent;
use Domain\Auth\Models\Device;

class NewDeviceDetectedEvent extends BaseEvent
{
    public function __construct(
        public Device $device,
    ) {}
}
