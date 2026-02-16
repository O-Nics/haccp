<?php

namespace Tests\TestHelpers;

use Domain\Auth\Data\DeviceData;
use Domain\Auth\Models\Device;

trait DeviceHelper
{
    public function getRandomDevice(): Device
    {
        return Device::factory()->make();
    }
}
