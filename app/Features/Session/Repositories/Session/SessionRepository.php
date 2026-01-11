<?php

declare(strict_types=1);

namespace App\Features\Session\Repositories\Session;

use App\Core\Repositories\BaseRepository;
use App\Models\Device;
use App\Models\Session;
use App\Models\User;

class SessionRepository extends BaseRepository implements SessionRepositoryInterface
{
    public function __construct(
    ) {
        $this->model = Session::class;
    }

    /**
     * Find an active session by device_id
     */
    public function findActiveByDeviceId(string $deviceId): ?Session
    {
        return $this->model
            ->whereHas('device', fn ($query) => $query->where('device_id', $deviceId))
            ->active()
            ->first();
    }

    /**
     * Find an active session by user and device
     */
    public function findActiveByUserAndDevice(User $user, Device $device): ?Session
    {
        return $user->sessions()
            ->where('device_id', $device->id)
            ->active()
            ->first();
    }

    /**
     * Create a new session
     */
    public function create(
        User $user,
        Device $device,
        string $refreshToken,
        int $expirationMinutes,
        ?int $accessTokenId
    ): Session {
        // Check if a session already exists for this user-device combination
        $existingSession = $user->sessions()
            ->where('device_id', $device->id)
            ->first();

        // If exists, revoke it (unique constraint enforcement)
        if ($existingSession) {
            $existingSession->revoke();
        }

        // Create new session
        $session = new Session;
        $session->user_id = $user->id;
        $session->device_id = $device->id;
        $session->access_token_id = $accessTokenId;
        $session->setRefreshToken($refreshToken);
        $session->refresh_token_expires_at = now()->addMinutes($expirationMinutes);
        $session->last_used_at = now();
        $session->save();

        // Store plain token temporarily for return
        $session->setAttribute('plain_refresh_token', $refreshToken);

        return $session;
    }

    /**
     * Rotate refresh token for a session
     */
    public function rotateRefreshToken(Session $session, int $expirationMinutes): string
    {
        $newRefreshToken = Session::generateRefreshToken();
        $session->setRefreshToken($newRefreshToken);
        $session->refresh_token_expires_at = now()->addMinutes($expirationMinutes);
        $session->touchLastUsed();
        $session->save();

        return $newRefreshToken;
    }

    /**
     * Revoke a session by user and device
     */
    public function revokeByUserAndDevice(User $user, Device $device): bool
    {
        $session = $this->findActiveByUserAndDevice($user, $device);

        if (! $session) {
            return false;
        }

        $session->revoke();

        return true;
    }

    /**
     * Revoke all sessions for a user except the current one
     */
    public function revokeAllExcept(User $user, Session $currentSession): int
    {
        $sessions = $user->sessions()
            ->active()
            ->where('id', '!=', $currentSession->id)
            ->get();

        $count = 0;
        foreach ($sessions as $session) {
            $session->revoke();
            $count++;
        }

        return $count;
    }

    /**
     * Delete a session
     */
    public function delete(Session $session): bool
    {
        return $session->delete();
    }
}
