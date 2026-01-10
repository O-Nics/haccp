<?php

declare(strict_types=1);

namespace App\Features\Device\Services;

use App\Core\Events\NewDeviceDetectedEvent;
use App\Features\Device\Data\DeviceInfoData;
use App\Features\Device\Repositories\UserDevice\UserDeviceRepositoryInterface;
use App\Models\User;
use App\Features\User\Repositories\User\UserRepositoryInterface;
use App\Models\Device;

final readonly class DeviceService
{
    public function __construct(
        private UserDeviceRepositoryInterface $deviceRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * Register a new device for the user or update existing
     */
    public function registerDevice(
        User $user,
        DeviceInfoData $deviceInfo,
        int $expirationMinutes,
        bool $isRegister = false
    ): Device {
        $existingDevice = $this->deviceRepository->findByUserAndDeviceId($user, $deviceInfo->deviceId);

        if ($existingDevice) {

            $device = $this->deviceRepository->update($existingDevice, $deviceInfo, $expirationMinutes);

        } else {

            $device = $this->deviceRepository->store($deviceInfo, $expirationMinutes, $user);

            if (! $isRegister) {
                NewDeviceDetectedEvent::dispatch($user, $device);
            }
        }

        return $device;
    }

    /**
     * Rotate the refresh token of a device and update expiration
     */
    public function rotateRefreshToken(Device $device, int $expirationMinutes): string
    {
        return $this->deviceRepository->rotateRefreshToken($device, $expirationMinutes);
    }

    /**
     * Revoke a specific device by device ID
     */
    public function revokeDevice(User $user, Device $device): bool
    {
        if ($device->user_id !== $user->id) {
            return false;
        }

        // Delete associated access tokens
        $this->userRepository->deleteTokensByDeviceId($user, $device->device_id);

        // Delete the device
        return $this->deviceRepository->delete($device);
    }

    /**
     * Revoke all devices except the current one
     */
    public function revokeAllDevicesExcept(User $user, Device $currentDevice): int
    {
        $devicesToRevoke = $this->deviceRepository->getAllDevicesExcept($user, $currentDevice);
        $count = 0;

        /** @var Device $device */
        foreach ($devicesToRevoke as $device) {
            // Delete associated access tokens
            $this->userRepository->deleteTokensByDeviceId($user, $device->device_id);

            // Delete the device
            $this->deviceRepository->delete($device);
            $count++;
        }

        return $count;
    }
}
