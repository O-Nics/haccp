<?php

declare(strict_types=1);

namespace Domain\Auth\Interfaces;

use Domain\Auth\Models\Device;
use Domain\Auth\Models\Session;
use Domain\Auth\Models\User;
use Domain\Auth\ValueObjects\TokenPair;

interface SessionRepositoryInterface
{
    public function createSession(User $user, Device $device, TokenPair $tokenPair): Session;

    public function deleteSession(Session $session): void;

    public function revokeAllForUser(User $user): void;
}
