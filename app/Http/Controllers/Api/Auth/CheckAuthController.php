<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Base\Controller;
use App\Http\Requests\Auth\CheckAuthRequest;
use App\Http\Resources\AuthResponse;
use App\Models\User;
use App\Services\Auth\AuthService;

class CheckAuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Check authentication
     *
     * Check if the current user is authenticated and retrieve their authentication data.
     *
     *
     * @unauthenticated
     */
    public function __invoke(CheckAuthRequest $request): AuthResponse
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
