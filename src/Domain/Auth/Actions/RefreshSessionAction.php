<?php

declare(strict_types=1);

namespace Domain\Auth\Actions;

use App\Core\Actions\BaseAction;
use Domain\Auth\Data\AuthData;
use Domain\Auth\Data\DeviceData;
use Domain\Auth\Data\TokenData;
use Domain\Auth\Enums\TokenAbilityEnum;
use Domain\Auth\Exceptions\InvalidRefreshTokenException;
use Domain\Auth\Interfaces\SessionRepositoryInterface;
use Domain\Auth\Interfaces\TokenRepositoryInterface;
use Domain\Auth\Models\User;

final readonly class RefreshSessionAction extends BaseAction
{
    public function __construct(
        private CreateAuthSessionAction $createAuthSession,
        private TokenRepositoryInterface $tokenRepository,
        private SessionRepositoryInterface $sessionRepository,
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

        $refreshToken = $session->refreshToken;

        if (! $refreshToken || $refreshToken->expires_at?->isPast()) {
            throw new InvalidRefreshTokenException;
        }

        $rotationEnabled = (bool) config('auth.tokens.refresh.rotation', true);

        if ($rotationEnabled) {
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

        $newAccessToken = $this->tokenRepository->issueAccessTokenFor(
            $user,
            [TokenAbilityEnum::ACCESS->value],
        );

        $this->sessionRepository->rotateAccessToken($session, $newAccessToken->tokenId());

        return new AuthData(
            user: $user,
            accessToken: new TokenData(
                token: $newAccessToken->plainToken(),
                expiresAt: $newAccessToken->expiresAt(),
            ),
            refreshToken: new TokenData(
                token: null,
                expiresAt: $refreshToken->expires_at->getTimestamp(),
            ),
        );
    }
}
