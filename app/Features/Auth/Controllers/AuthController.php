<?php

// app/Http/Controllers/Api/AuthController.php

declare(strict_types=1);

namespace App\Features\Auth\Controllers;

use App\Exceptions\InvalidCredentialsException;
use App\Http\Controllers\Base\Controller;
use App\Http\Requests\Auth\CheckAuthRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LogoutRequest;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\AuthResponse;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
