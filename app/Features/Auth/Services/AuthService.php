<?php

declare(strict_types=1);

namespace App\Features\Auth\Services;

use App\Core\Exceptions\InvalidCredentialsException;
use App\Core\Exceptions\InvalidRefreshTokenException;
use App\Features\Auth\Data\AuthData;
use App\Features\Auth\Data\CheckAuthData;
use App\Features\Auth\Data\DeviceData;
use App\Features\Auth\Data\LoginData;
use App\Features\Auth\Data\RefreshTokenData;
use App\Features\Auth\Data\RegisterData;
use App\Features\Auth\Data\TokenData;
use App\Features\Auth\Enums\TokenAbilityEnum;
use App\Features\Auth\Notifications\UserRegisterNotification;
use App\Features\Auth\Repositories\Auth\AuthRepositoryInterface;
use App\Features\User\Repositories\User\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

final readonly class AuthService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private AuthRepositoryInterface $authRepository,
        private TokenService $tokenService,
    ) {}

    /**
     * Authenticate user and generate tokens
     *
     * @throws InvalidCredentialsException
     */
    public function login(
        LoginData $data,
    ): AuthData {

        $user = $this->userRepository->findByEmail($data->email);

        if (! $user || ! Hash::check($data->password, $user->password)) {
            throw new InvalidCredentialsException;
        }

        return $this->generateSession($user, $data->deviceData, $data->activeBusiness);
    }

    public function checkAuth(
        CheckAuthData $data,
        User $user,
    ): AuthData {
        return $this->generateSession($user, $data->deviceInfo, null, $data->activeBusinessId);
    }

    /**
     * Register a new user
     */
    public function register(
        RegisterData $data,
    ): AuthData {
        $user = $this->userRepository->store($data->storeUserData);

        $user->notify(new UserRegisterNotification);

        return $this->generateSession($user, $data->deviceInfo, null);
    }

    /**
     * Generate tokens for a user
     */
    private function generateSession(
        User $user,
        DeviceData $deviceInfo,
        ?string $activeBusinessId,
    ): AuthData {

        $device = $this->authRepository->createOrUpdateDevice(user: $user, deviceData: $deviceInfo);

        $tokens = $this->tokenService->issueFor(
            $user,
            [TokenAbilityEnum::ACCESS->value],
            [TokenAbilityEnum::REFRESH->value],
            rememberMe: $device->remember_me
        );


        if ($device->session) {
            $this->authRepository->deleteSession($device->session);
        }

        $this->authRepository->createSession($user, $device, $tokens);

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

    /**
     * Refresh tokens
     *
     * @throws InvalidRefreshTokenException
     */
    public function refresh(User $user): AuthData
    {
        $session = $user->currentSession();
        if(! $session) {
            throw new InvalidRefreshTokenException;
        }
        $device = $session->device;

        $deviceData = new DeviceData(
            deviceId: $device->device_id,
            devicePlatform: $device->platform,
            deviceModel: $device->model,
            rememberMe: $device->remember_me,
            ip: $device->ip_address,
        );

        return $this->generateSession($user, $deviceData, null);
    }

    /**
     * Logout (revoke session and tokens)
     */
    public function logout(User $user): void
    {
        $session = $user->currentSession();
        if ($session) {
            $this->authRepository->deleteSession($session);
        }
    }
}
