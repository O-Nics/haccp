<?php

declare(strict_types=1);

namespace Domain\Auth\Interfaces;

use Domain\Auth\Data\DeviceData;
use Domain\Auth\Models\Device;
use Domain\Auth\Models\User;

interface DeviceRepositoryInterface
{
    public function findByDeviceData(User $user, DeviceData $deviceData): ?Device;

    public function create(User $user, DeviceData $deviceData): Device;

    public function update(Device $device, DeviceData $deviceData): Device;
}
