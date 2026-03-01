<?php

declare(strict_types=1);

namespace Infrastructure\Auth\Repositories;

use App\Core\Repositories\BaseRepository;
use Domain\Auth\Data\DeviceData;
use Domain\Auth\Interfaces\DeviceRepositoryInterface;
use Domain\Auth\Models\Device;
use Domain\Auth\Models\User;
use Throwable;

final readonly class DeviceRepository extends BaseRepository implements DeviceRepositoryInterface
{
    public function __construct(
        private Device $model
    ) {}

    /**
     * Finds an existing device for the user matching the device ID and platform. Returns null if not found.
     *
     * @param  User  $user  The owner of the device.
     * @param  DeviceData  $deviceData  The device identifiers to match against.
     *
     * @throws Throwable
     */
    public function findByDeviceData(User $user, DeviceData $deviceData): ?Device
    {
        return $this->model->query()
            ->where('device_id', $deviceData->deviceId)
            ->where('platform', $deviceData->devicePlatform)
            ->where('user_id', $user->id)
            ->first();
    }

    /**
     * Updates the IP address and remember_me flag of an existing device.
     *
     * @param  Device  $device  The device record to update.
     * @param  DeviceData  $deviceData  The updated device data.
     *
     * @throws Throwable
     */
    public function update(Device $device, DeviceData $deviceData): Device
    {
        $device->ip_address = $deviceData->ip;
        if ($deviceData->rememberMe !== null) {
            $device->remember_me = $deviceData->rememberMe;
        }

        $device->saveOrFail();

        return $device;
    }

    /**
     * Persists a new device for the given user from the provided data.
     *
     * @param  User  $user  The owner of the device.
     * @param  DeviceData  $deviceData  The data to populate the new device record.
     *
     * @throws Throwable
     */
    public function create(User $user, DeviceData $deviceData): Device
    {
        $device = $this->model->newInstance();
        $device->device_id = $deviceData->deviceId;
        $device->platform = $deviceData->devicePlatform;
        $device->model = $deviceData->deviceModel;
        $device->ip_address = $deviceData->ip;
        $device->remember_me = $deviceData->rememberMe;
        $device->user()->associate($user);

        $device->saveOrFail();

        return $device;
    }
}
