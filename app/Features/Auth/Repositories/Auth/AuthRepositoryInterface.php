<?php

namespace App\Features\Auth\Repositories\Auth;

use App\Features\Auth\Data\DeviceData;
use App\Features\Auth\Services\TokenPair;
use App\Models\Device;
use App\Models\Session;
use App\Models\User;

interface AuthRepositoryInterface
{
    public function createOrUpdateDevice(User $user, DeviceData $deviceData): Device;

    public function createSession(User $user, Device $device, TokenPair $tokenPair): Session;

    public function deleteSession(Session $session): void;
}
