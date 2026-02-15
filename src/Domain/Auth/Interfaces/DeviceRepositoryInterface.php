<?php

declare(strict_types=1);

namespace Domain\Auth\Interfaces;

use Domain\Auth\Data\DeviceData;
use Domain\Auth\Models\Device;
use Domain\Auth\Models\User;

interface DeviceRepositoryInterface
{
    public function createOrUpdateDevice(User $user, DeviceData $deviceData): Device;
}
