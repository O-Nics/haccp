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
use App\Features\Auth\Data\TokenData;
use App\Features\Device\Data\DeviceInfoData;
use App\Features\Device\Repositories\UserDevice\UserDeviceRepositoryInterface;
use App\Features\Device\Services\DeviceService;
use App\Models\User;
use App\Features\User\Repositories\User\UserRepositoryInterface;
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

        return $this->generateTokens($user, $data->deviceInfo, $data->rememberMe, $data->activeBusiness, isRegister: false);
    }

    public function checkAuth(
        CheckAuthData $data,
        User $user,
    ): AuthData {
        return $this->generateTokens($user, $data->deviceInfo, $data->rememberMe, $data->activeBusiness);
    }

    /**
     * Register a new user
     */
    public function register(
        RegisterData $data,
    ): AuthData {
        $user = $this->userRepository->store($data);

        return $this->generateTokens($user, $data->deviceInfo, $data->rememberMe, null, isRegister: true);
    }

    /**
     * Generate tokens for a user
     */
    private function generateTokens(
        User $user,
        DeviceInfoData $deviceInfo,
        bool $rememberMe,
        ?string $activeBusiness,
        bool $isRegister = false,
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

        $business = $activeBusiness && $user->businesses()->where('businesses.id', $activeBusiness)->exists()
            ? $user->businesses()->where('businesses.id', $activeBusiness)->first()
            : null;

        $this->userRepository->updateLastLogin($user);

        return new AuthData(
            user: $user,
            accessToken: new TokenData(
                token: $accessToken,
                expiresIn: $accessTokenExpiration * 60,
                tokenType: 'Bearer',
            ),
            refreshToken: new TokenData(
                token: $device->getAttribute('plain_refresh_token'),
                expiresIn: $expirationMinutes * 60,
                tokenType: 'refresh',
            ),
            activeBusiness: $business,
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
            throw new InvalidRefreshTokenException;
        }

        if ($device->isTokenExpired()) {
            throw new InvalidRefreshTokenException();
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
            user: $user,
            accessToken: new TokenData(
                token: $accessToken,
                expiresIn: $accessTokenExpiration * 60,
                tokenType: 'Bearer',
            ),
            refreshToken: new TokenData(
                token: $newRefreshToken,
                expiresIn: $expirationMinutes * 60,
                tokenType: 'refresh',
            ),
            activeBusiness: null,
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
