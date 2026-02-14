<?php

namespace App\Features\Auth\Repositories\Auth;

use App\Features\Auth\Services\TokenPair;
use Domain\Auth\Data\DeviceData;
use Domain\Auth\Models\Device;
use Domain\Auth\Models\Session;
use Domain\Auth\Models\User;

interface AuthRepositoryInterface
{
    public function createOrUpdateDevice(User $user, DeviceData $deviceData): Device;

    public function createSession(User $user, Device $device, TokenPair $tokenPair): Session;

    public function deleteSession(Session $session): void;
}
