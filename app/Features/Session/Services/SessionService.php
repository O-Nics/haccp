<?php

declare(strict_types=1);

namespace App\Features\Session\Services;

use App\Core\Exceptions\InvalidRefreshTokenException;
use App\Features\Auth\Data\TokenData;
use App\Features\Session\Data\SessionData;
use App\Features\Session\Repositories\Session\SessionRepositoryInterface;
use App\Models\Device;
use App\Models\Session;
use App\Models\User;

final readonly class SessionService
{
    public function __construct(
        private SessionRepositoryInterface $sessionRepository
    ) {}

    /**
     * Create a new session with access and refresh tokens
     *
     * @return SessionData
     */
    public function createSession(User $user, Device $device, int $expirationMinutes): SessionData
    {
        // 1. Create access token via Sanctum (15 min)
        $accessTokenExpiration = config('auth.tokens.access.expiration');
        $accessTokenData = $user->createToken(
            name: $device->device_id,
            abilities: ['access'],
            expiresAt: now()->addMinutes($accessTokenExpiration)
        );

        // 2. Generate refresh token (plain, 64 characters)
        $refreshToken = Session::generateRefreshToken();

        // 3. Create session record with FK to access token
        $session = $this->sessionRepository->create(
            user: $user,
            device: $device,
            refreshToken: $refreshToken,
            expirationMinutes: $expirationMinutes,
            accessTokenId: $accessTokenData->accessToken->id
        );

        // 4. Return session and plain tokens
        return new SessionData(
            session: $session,
            accessToken: new TokenData(
                token: $accessTokenData->plainTextToken,
                expiresAt: config('auth.tokens.access.expiration'),
                tokenType: 'access',
            ),
            refreshToken: new TokenData(
                token: $refreshToken,
                expiresAt: $expirationMinutes,
                tokenType: 'refresh',
            ),
        );
    }

    /**
     * Refresh a session by rotating tokens
     *
     * @return SessionData
     *
     * @throws InvalidRefreshTokenException
     */
    public function refreshSession(string $deviceId, string $refreshToken): SessionData
    {
        $session = $this->sessionRepository->findActiveByDeviceId($deviceId);

        if (! $session) {
            throw new InvalidRefreshTokenException('Session not found');
        }

        if ($session->isRevoked()) {
            throw new InvalidRefreshTokenException('Session is revoked');
        }

        if ($session->isTokenExpired()) {
            throw new InvalidRefreshTokenException('Refresh token expired');
        }

        if (! $session->verifyRefreshToken($refreshToken)) {
            throw new InvalidRefreshTokenException('Invalid refresh token');
        }

        $expirationMinutes = $session->created_at && $session->refresh_token_expires_at
            ? (int) $session->created_at->diffInMinutes($session->refresh_token_expires_at, absolute: true)
            : config('auth.tokens.refresh.expiration');

        $accessTokenExpiration = config('auth.tokens.access.expiration');
        $accessTokenData = $session->user->createToken(
            name: $session->device->device_id,
            abilities: ['access'],
            expiresAt: now()->addMinutes($accessTokenExpiration)
        );

        $newRefreshToken = config('auth.tokens.refresh.rotation')
            ? $this->sessionRepository->rotateRefreshToken($session, $expirationMinutes)
            : $refreshToken;

        $session->access_token_id = $accessTokenData->accessToken->id;
        $session->touchLastUsed();
        $session->save();

        return new SessionData(
            session: $session,
            accessToken: new TokenData(
                token: $accessTokenData->plainTextToken,
                expiresAt: $accessTokenExpiration,
                tokenType: 'access',
            ),
            refreshToken: new TokenData(
                token: $newRefreshToken,
                expiresAt: $expirationMinutes,
                tokenType: 'refresh',
            ),
        );
    }

    /**
     * Revoke a session (logout)
     */
    public function revokeSession(User $user, Device $device): void
    {
        $session = $this->sessionRepository->findActiveByUserAndDevice($user, $device);

        if (! $session) {
            return;
        }

        $session->revoke();

        if ($session->accessToken) {
            $session->accessToken->delete();
        }
    }

    /**
     * Revoke all sessions for a user except the current one
     */
    public function revokeAllSessionsExcept(User $user, Session $currentSession): int
    {
        return $this->sessionRepository->revokeAllExcept($user, $currentSession);
    }
}
