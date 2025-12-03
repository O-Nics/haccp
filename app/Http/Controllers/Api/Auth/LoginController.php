<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\InvalidCredentialsException;
use App\Http\Controllers\Base\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\AuthResponse;
use App\Models\User;
use App\Services\Auth\AuthService;

class LoginController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Login user
     *
     * Login a user with the provided credentials and device information.
     *
     * @throws InvalidCredentialsException
     *
     * @unauthenticated
     */
    public function __invoke(LoginRequest $request): AuthResponse
    {
        $authData = $this->authService->login(
            data: $request->toDto(),
        );

        return new AuthResponse($authData);
    }
}
