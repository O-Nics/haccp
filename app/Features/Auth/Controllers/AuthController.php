<?php

// app/Http/Controllers/Api/AuthController.php

declare(strict_types=1);

namespace App\Features\Auth\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Exceptions\InvalidCredentialsException;
use App\Features\Auth\Resources\AuthResponse;
use App\Features\Auth\Services\AuthService;
use App\Features\Auth\Requests\CheckAuthRequest;
use App\Features\Auth\Requests\LoginRequest;
use App\Features\Auth\Requests\LogoutRequest;
use App\Features\Auth\Requests\RefreshTokenRequest;
use App\Features\Auth\Requests\RegisterRequest;
use Illuminate\Http\Response;

final class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Register a new user
     *
     * Returns an authentication response after registering a new user with the provided registration data and device information.
     * @unauthenticated
     */
    public function register(RegisterRequest $request): AuthResponse
    {
        $authData = $this->authService->register(
            data: $request->toDto(),
        );

        return new AuthResponse($authData);

    }

    /**
     * Login user
     *
     * Returns an authentication response after logging in a user with the provided credentials and device information.
     * @unauthenticated
     *
     * @throws InvalidCredentialsException
     */
    public function login(LoginRequest $request): AuthResponse
    {
        $authData = $this->authService->login(
            data: $request->toDto(),
        );

        return new AuthResponse($authData);
    }

    /**
     * Refresh tokens
     *
     * Returns a new authentication response with refreshed tokens using the provided refresh token data.
     * @unauthenticated
     *
     * @throws InvalidCredentialsException
     */
    public function refresh(RefreshTokenRequest $request): AuthResponse
    {
        $authData = $this->authService->refresh(
            refreshTokenData: $request->toDto()
        );

        return new AuthResponse($authData);
    }

    /**
     * Logout user
     *
     * Logs out the user by invalidating the session associated with the provided device ID.
     * @unauthenticated
     */
    public function logout(LogoutRequest $request): Response
    {

        $this->authService->logout(
            user: $request->user(),
            deviceId: $request->toDto()->deviceId
        );

        return response()->noContent();
    }

    /**
     * Check authentication status
     *
     * Returns the authentication status and user information for the currently authenticated user.
     */
    public function checkAuth(CheckAuthRequest $request): AuthResponse
    {
        $user = auth()->user();
        $authData = $this->authService->checkAuth(
            data: $request->toDto(),
            user: $user,
        );


        $authData->user->load([
            'businesses',
        ]);

        return new AuthResponse($authData);
    }

}
