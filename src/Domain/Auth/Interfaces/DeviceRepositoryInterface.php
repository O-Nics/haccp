<?php

declare(strict_types=1);

namespace Domain\Auth\Interfaces;

use Domain\Auth\Data\DeviceData;
use Domain\Auth\Models\Device;
use Domain\Auth\Models\User;
use Throwable;

interface DeviceRepositoryInterface
{
    /**
     * Finds an existing device for the user matching the device ID and platform. Returns null if not found.
     *
     * @param  User  $user  The owner of the device.
     * @param  DeviceData  $deviceData  The device identifiers to match against.
     */
    public function findByDeviceData(User $user, DeviceData $deviceData): ?Device;

    /**
     * Persists a new device for the given user from the provided data.
     *
     * @param  User  $user  The owner of the device.
     * @param  DeviceData  $deviceData  The data to populate the new device record.
     *
     * @throws Throwable
     */
    public function create(User $user, DeviceData $deviceData): Device;

    /**
     * Updates the IP address and remember_me flag of an existing device.
     *
     * @param  Device  $device  The device record to update.
     * @param  DeviceData  $deviceData  The updated device data.
     *
     * @throws Throwable
     */
    public function update(Device $device, DeviceData $deviceData): Device;
}
