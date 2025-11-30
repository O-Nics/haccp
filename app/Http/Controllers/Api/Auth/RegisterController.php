<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Base\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Device\DeviceRequest;
use App\Http\Resources\AuthResponse;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Register a new user
     *
     * Creates a new user account with the provided registration data and device information.
     *
     * @unauthenticated
     */
    public function __invoke(RegisterRequest $request): AuthResponse
    {
        $authData = $this->authService->register(
            data: $request->toDto(),
        );

        return new AuthResponse($authData);
    }
}
