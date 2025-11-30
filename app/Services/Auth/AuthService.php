<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\DataTransferObjects\Auth\LoginData;
use App\DataTransferObjects\Auth\RefreshTokenData;
use App\DataTransferObjects\Auth\RegisterData;
use App\DataTransferObjects\DeviceInfoData;
use App\DataTransferObjects\AuthData;
use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserDevice\UserDeviceRepositoryInterface;
use App\Services\Device\DeviceService;
use Illuminate\Support\Facades\Hash;

final readonly class AuthService
{
    public function __construct(
        private DeviceService $deviceService,
        private UserRepositoryInterface $userRepository,
        private UserDeviceRepositoryInterface $deviceRepository,
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

        return $this->generateTokens($user, $data->deviceInfo, $data->rememberMe);
    }

    /**
     * Register a new user
     */
    public function register(
        RegisterData $data,
    ): AuthData {
        $user = $this->userRepository->store($data);

        return $this->generateTokens($user, $data->deviceInfo, $data->remember_me, isRegister: true);
    }

    /**
     * Generate tokens for a user
     */
    private function generateTokens(
        User $user,
        DeviceInfoData $deviceInfo,
        bool $rememberMe = false,
        bool $isRegister = false
    ): AuthData {

        $tokenType = $rememberMe ? 'remember' : 'refresh';
        $expirationMinutes = config("auth.tokens.{$tokenType}.expiration");

        $device = $this->deviceService->registerDevice($user, $deviceInfo, $expirationMinutes, $isRegister);

        $accessTokenExpiration = config('auth.tokens.access.expiration');

        $this->userRepository->deleteTokensByDeviceId($user, $device->device_id);

        $accessToken = $user->createToken(
            name: $device->device_id,
            expiresAt: now()->addMinutes($accessTokenExpiration)
        )->plainTextToken;

        $this->userRepository->updateLastLogin($user);

        return new AuthData(
            user: $user,
            accessToken: $accessToken,
            accessTokenExpiresIn: $accessTokenExpiration * 60,
            refreshToken: $device->getAttribute('plain_refresh_token'),
            refreshTokenExpiresIn: $expirationMinutes * 60,
            deviceId: $device->device_id,
        );
    }

    /**
     * Refresh tokens
     *
     * @throws InvalidCredentialsException
     */
    public function refresh(RefreshTokenData $refreshTokenData): AuthData
    {
        // Find device by device_id first
        $device = $this->deviceRepository->findByDeviceId($refreshTokenData->deviceId);

        if (! $device) {
            throw new InvalidCredentialsException;
        }

        $user = $device->user;

        if (! $user instanceof User) {
            throw new InvalidCredentialsException;
        }

        if (! $device->verifyRefreshToken($refreshTokenData->refreshToken)) {
            throw new InvalidCredentialsException;
        }

        if ($device->isTokenExpired()) {
            throw new InvalidCredentialsException;
        }

        $expirationMinutes = $device->created_at && $device->token_expires_at
            ? (int) $device->created_at->diffInMinutes($device->token_expires_at, absolute: true)
            : config('auth.tokens.refresh.expiration');

        $newRefreshToken = config('auth.tokens.refresh.rotation')
            ? $this->deviceService->rotateRefreshToken($device, $expirationMinutes)
            : $refreshTokenData->refreshToken;

        $accessTokenExpiration = config('auth.tokens.access.expiration');
        $accessToken = $user->createToken(
            name: $device->device_name,
            abilities: ['*'],
            expiresAt: now()->addMinutes($accessTokenExpiration)
        )->plainTextToken;

        return new AuthData(
            accessToken: $accessToken,
            accessTokenExpiresIn: $accessTokenExpiration * 60,
            refreshToken: $newRefreshToken,
            refreshTokenExpiresIn: $expirationMinutes * 60,
            deviceId: $device->device_id,
        );
    }

    /**
     * Logout (revoke device and tokens)
     */
    public function logout(User $user, string $deviceId): void
    {
        // Get the device before revoking
        $device = $this->deviceRepository->findByUserAndDeviceId($user, $deviceId);

        // Revoke the device
        if ($device) {
            $this->deviceService->revokeDevice($user, $device);
        }

        // Also delete current access token as fallback
        $user->currentAccessToken()?->delete();
    }
}
