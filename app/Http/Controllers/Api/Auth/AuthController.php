<?php

// app/Http/Controllers/Api/AuthController.php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\InvalidCredentialsException;
use App\Http\Controllers\Base\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LogoutRequest;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\AuthResponse;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Register a new user
     *
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
     * @unauthenticated
     */
    public function logout(LogoutRequest $request): JsonResponse
    {

        $this->authService->logout(
            user: $request->user(),
            deviceId: $request->toDto()->deviceId
        );

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request): UserResource
    {
        return UserResource::make($request->user());
    }
}
