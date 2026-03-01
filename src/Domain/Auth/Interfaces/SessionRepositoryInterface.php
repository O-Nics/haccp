<?php

declare(strict_types=1);

namespace Domain\Auth\Interfaces;

use Domain\Auth\Models\Device;
use Domain\Auth\Models\Session;
use Domain\Auth\Models\User;
use Domain\Auth\ValueObjects\TokenPair;

interface SessionRepositoryInterface
{
    /**
     * Creates and persists a new auth session linking the user, device, and token pair.
     *
     * @param  User      $user      The authenticated user.
     * @param  Device    $device    The device the session is created on.
     * @param  TokenPair $tokenPair The issued access and refresh token pair.
     * @return Session
     * @throws \Throwable
     */
    public function createSession(User $user, Device $device, TokenPair $tokenPair): Session;

    /**
     * Revokes and hard-deletes the session along with its access and refresh tokens.
     *
     * @param  Session $session The session to delete.
     */
    public function deleteSession(Session $session): void;

    /**
     * Revokes all active sessions for the given user.
     *
     * @param  User $user The user whose sessions to revoke.
     */
    public function revokeAllForUser(User $user): void;

    /**
     * Replaces the session's access token with a new one and deletes the old token.
     *
     * @param  Session $session          The session to update.
     * @param  int     $newAccessTokenId The ID of the newly issued access token.
     */
    public function rotateAccessToken(Session $session, int $newAccessTokenId): void;
}
