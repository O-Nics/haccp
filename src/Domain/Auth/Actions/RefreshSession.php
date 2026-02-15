<?php

declare(strict_types=1);

namespace Domain\Auth\Actions;

use App\Core\Exceptions\InvalidRefreshTokenException;
use Domain\Auth\Data\AuthData;
use Domain\Auth\Data\DeviceData;
use Domain\Auth\Models\User;

final readonly class RefreshSession
{
    public function __construct(
        private CreateAuthSession $createAuthSession,
    ) {}

    /**
     * @throws InvalidRefreshTokenException
     */
    public function handle(User $user): AuthData
    {
        $session = $user->currentSession();

        if (! $session) {
            throw new InvalidRefreshTokenException;
        }

        $device = $session->device;

        $deviceData = new DeviceData(
            deviceId: $device->device_id,
            devicePlatform: $device->platform,
            deviceModel: $device->model,
            rememberMe: $device->remember_me,
            ip: $device->ip_address,
        );

        return $this->createAuthSession->handle($user, $deviceData);
    }
}
