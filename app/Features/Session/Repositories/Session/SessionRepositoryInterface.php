<?php

declare(strict_types=1);

namespace App\Features\Session\Repositories\Session;

use App\Models\Device;
use App\Models\Session;
use App\Models\User;

interface SessionRepositoryInterface
{
    /**
     * Find an active session by device_id
     */
    public function findActiveByDeviceId(string $deviceId): ?Session;

    /**
     * Find an active session by user and device
     */
    public function findActiveByUserAndDevice(User $user, Device $device): ?Session;

    /**
     * Create a new session
     */
    public function create(User $user, Device $device, string $refreshToken, int $expirationMinutes, ?int $accessTokenId): Session;

    /**
     * Rotate refresh token for a session
     */
    public function rotateRefreshToken(Session $session, int $expirationMinutes): string;

    /**
     * Revoke a session by user and device
     */
    public function revokeByUserAndDevice(User $user, Device $device): bool;

    /**
     * Revoke all sessions for a user except the current one
     */
    public function revokeAllExcept(User $user, Session $currentSession): int;

    /**
     * Delete a session
     */
    public function delete(Session $session): bool;
}
