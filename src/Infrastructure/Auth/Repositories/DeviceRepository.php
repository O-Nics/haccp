<?php

declare(strict_types=1);

namespace Infrastructure\Auth\Repositories;

use Domain\Auth\Data\DeviceData;
use Domain\Auth\Interfaces\DeviceRepositoryInterface;
use Domain\Auth\Models\Device;
use Domain\Auth\Models\User;
use Throwable;

final readonly class DeviceRepository implements DeviceRepositoryInterface
{
    /**
     * @throws Throwable
     */
    public function createOrUpdateDevice(User $user, DeviceData $deviceData): Device
    {
        $device = Device::query()
            ->where('device_id', $deviceData->deviceId)
            ->where('platform', $deviceData->devicePlatform)
            ->where('user_id', $user->id)
            ->first();

        if (! $device) {
            $device = new Device;
            $device->device_id = $deviceData->deviceId;
            $device->platform = $deviceData->devicePlatform;
            $device->model = $deviceData->deviceModel;
            $device->ip_address = $deviceData->ip;
            $device->remember_me = $deviceData->rememberMe;
            $device->user()->associate($user);
        } else {
            $device->ip_address = $deviceData->ip;
            if ($deviceData->rememberMe !== null) {
                $device->remember_me = $deviceData->rememberMe;
            }
        }

        $device->saveOrFail();

        return $device;
    }
}
