<?php

declare(strict_types=1);

namespace App\Features\Session\Resources;

use App\Core\Events\NewDeviceDetectedEvent;
use App\Features\Session\Data\DeviceInfoData;
use App\Features\Session\Repositories\Device\DeviceRepositoryInterface;
use App\Models\Device;
use App\Models\User;

final readonly class DeviceService
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
    ) {}

    /**
     * Register a new device for the user or update existing (fingerprinting only)
     */
    public function registerDevice(
        User $user,
        DeviceInfoData $deviceInfo,
        bool $isRegister = false
    ): Device {
        $existingDevice = $this->deviceRepository->findByUserAndDeviceId($user, $deviceInfo->deviceId);

        if ($existingDevice) {
            $device = $this->deviceRepository->update($existingDevice, $deviceInfo);
        } else {
            $device = $this->deviceRepository->store($deviceInfo, $user);

            if (! $isRegister) {
                NewDeviceDetectedEvent::dispatch($user, $device);
            }
        }

        return $device;
    }
}
