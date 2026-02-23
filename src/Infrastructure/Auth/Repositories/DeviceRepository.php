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
