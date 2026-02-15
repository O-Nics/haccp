<?php

declare(strict_types=1);

namespace Domain\Auth\Actions;

use App\Core\Actions\BaseAction;
use Domain\Auth\Data\AuthData;
use Domain\Auth\Data\DeviceData;
use Domain\Auth\Data\TokenData;
use Domain\Auth\Enums\TokenAbilityEnum;
use Domain\Auth\Events\NewDeviceDetectedEvent;
use Domain\Auth\Interfaces\DeviceRepositoryInterface;
use Domain\Auth\Interfaces\SessionRepositoryInterface;
use Domain\Auth\Interfaces\TokenRepositoryInterface;
use Domain\Auth\Interfaces\UserRepositoryInterface;
use Domain\Auth\Models\User;

final readonly class CreateAuthSession extends BaseAction
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private SessionRepositoryInterface $sessionRepository,
        private TokenRepositoryInterface $tokenRepository,
        private UserRepositoryInterface $userRepository,
    ) {}

    public function handle(
        User $user,
        DeviceData $deviceInfo,
        ?string $activeBusinessId = null,
    ): AuthData {
        $device = $this->deviceRepository->createOrUpdateDevice(user: $user, deviceData: $deviceInfo);

        if ($device->wasRecentlyCreated) {
            event(new NewDeviceDetectedEvent($user, $device));
        }

        $tokens = $this->tokenRepository->issueFor(
            $user,
            [TokenAbilityEnum::ACCESS->value],
            [TokenAbilityEnum::REFRESH->value],
            rememberMe: $device->remember_me
        );

        if ($device->session) {
            $this->sessionRepository->deleteSession($device->session);
        }

        $this->sessionRepository->createSession($user, $device, $tokens);

        $business = null;
        $businessesQuery = $user->businesses();

        if ($activeBusinessId === null) {
            if ($businessesQuery->count() === 1) {
                $business = $businessesQuery->first();
            }
        } else {
            $business = $businessesQuery
                ->where('businesses.id', $activeBusinessId)
                ->first();
        }

        $this->userRepository->updateLastLogin($user);

        return new AuthData(
            user: $user,
            accessToken: new TokenData(
                token: $tokens->accessPlain(),
                expiresAt: $tokens->accessExpiresAt(),
            ),
            refreshToken: new TokenData(
                token: $tokens->refreshPlain(),
                expiresAt: $tokens->refreshExpiresAt(),
            ),
            activeBusiness: $business,
        );
    }
}
