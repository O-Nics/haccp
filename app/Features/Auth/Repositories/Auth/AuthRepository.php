<?php

namespace App\Features\Auth\Repositories\Auth;

use App\Features\Auth\Services\TokenPair;
use Domain\Auth\Data\DeviceData;
use Domain\Auth\Models\Device;
use Domain\Auth\Models\Session;
use Domain\Auth\Models\User;
use Throwable;

class AuthRepository implements AuthRepositoryInterface
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

    /**
     * @throws Throwable
     */
    public function createSession(User $user, Device $device, TokenPair $tokenPair): Session
    {

        $session = new Session;
        $session->device()->associate($device);
        $session->user()->associate($user);
        $session->access_token_id = $tokenPair->accessId();
        $session->refresh_token_id = $tokenPair->refreshId();
        $session->saveOrFail();

        return $session;
    }

    public function deleteSession(Session $session): void
    {
        $session->accessToken()->delete();
        $session->refreshToken()->delete();
        $session->delete();
    }
}
