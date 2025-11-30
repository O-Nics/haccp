<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Base\Controller;
use App\Http\Requests\Auth\LogoutRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;

class LogoutController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Logout user
     *
     * Logs out the authenticated user from the specified device.
     *
     * @unauthenticated
     */
    public function __invoke(LogoutRequest $request): JsonResponse
    {
        $this->authService->logout(
            user: auth()->user(),
            deviceId: $request->toDto()->deviceId
        );

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
