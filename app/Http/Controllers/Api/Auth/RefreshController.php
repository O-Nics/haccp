<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\InvalidCredentialsException;
use App\Http\Controllers\Base\Controller;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Models\User;
use App\Services\Auth\AuthService;

class RefreshController extends Controller
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
    public function __invoke(RefreshTokenRequest $request): array
    {
        $tokens = $this->authService->refresh(
            refreshTokenData: $request->toDto()
        );

        return $tokens->toApiResponse();
    }
}
