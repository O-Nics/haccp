<?php

declare(strict_types=1);

namespace App\Features\Auth\Services;

use App\Core\Exceptions\InvalidCredentialsException;
use App\Core\Exceptions\InvalidRefreshTokenException;
use App\Features\Auth\Data\AuthData;
use App\Features\Auth\Data\CheckAuthData;
use App\Features\Auth\Data\LoginData;
use App\Features\Auth\Data\RefreshTokenData;
use App\Features\Auth\Data\RegisterData;
use App\Features\Auth\Notifications\UserRegisterNotification;
use App\Features\Session\Data\DeviceInfoData;
use App\Features\Session\Repositories\Device\DeviceRepositoryInterface;
use App\Features\Session\Resources\DeviceService;
use App\Features\Session\Services\SessionService;
use App\Features\User\Repositories\User\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

final readonly class AuthService
{
    public function __construct(
        private DeviceService $deviceService,
        private SessionService $sessionService,
        private UserRepositoryInterface $userRepository,
        private DeviceRepositoryInterface $deviceRepository,
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

        return $this->generateSession($user, $data->deviceInfo, $data->rememberMe, $data->activeBusiness);
    }

    public function checkAuth(
        CheckAuthData $data,
        User $user,
    ): AuthData {
        return $this->generateSession($user, $data->deviceInfo, $data->rememberMe, $data->activeBusinessId);
    }

    /**
     * Register a new user
     */
    public function register(
        RegisterData $data,
    ): AuthData {
        $user = $this->userRepository->store($data->storeUserData);

        $user->notify(new UserRegisterNotification);

        return $this->generateSession($user, $data->deviceInfo, $data->rememberMe, null, isRegister: true);
    }

    /**
     * Generate tokens for a user
     */
    private function generateSession(
        User $user,
        DeviceInfoData $deviceInfo,
        bool $rememberMe,
        ?string $activeBusinessId,
        bool $isRegister = false,
    ): AuthData {
        $tokenType = $rememberMe ? 'remember' : 'refresh';
        $expirationMinutes = config("auth.tokens.{$tokenType}.expiration");

        $device = $this->deviceService->registerDevice($user, $deviceInfo, $isRegister);

        $this->userRepository->deleteTokensByDeviceId($user, $device->device_id);

        $result = $this->sessionService->createSession($user, $device, $expirationMinutes);

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
            accessToken: $result->accessToken,
            refreshToken: $result->refreshToken,
            activeBusiness: $business,
        );
    }

    /**
     * Refresh tokens
     *
     * @throws InvalidRefreshTokenException
     */
    public function refresh(RefreshTokenData $refreshTokenData): AuthData
    {
        $result = $this->sessionService->refreshSession(
            deviceId: $refreshTokenData->deviceId,
            refreshToken: $refreshTokenData->refreshToken
        );

        return new AuthData(
            user: $result->session->user,
            accessToken: $result->accessToken,
            refreshToken: $result->refreshToken,
            activeBusiness: null,
        );
    }

    /**
     * Logout (revoke session and tokens)
     */
    public function logout(User $user, string $deviceId): void
    {
        $device = $this->deviceRepository->findByUserAndDeviceId($user, $deviceId);

        if ($device) {
            $this->sessionService->revokeSession($user, $device);
        }

        $user->currentAccessToken()?->delete();
    }
}
