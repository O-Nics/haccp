<?php

declare(strict_types=1);

namespace App\Modules\Auth\Controllers;

use App\Core\Controllers\BaseController;
use App\Modules\Auth\Requests\CheckAuthRequest;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Resources\AuthResponseResource;
use Domain\Auth\Actions\CheckAuth;
use Domain\Auth\Actions\LoginUser;
use Domain\Auth\Actions\LogoutUser;
use Domain\Auth\Actions\RefreshSession;
use Domain\Auth\Actions\RegisterUser;
use Domain\Auth\Exceptions\InvalidCredentialsException;
use Domain\Auth\Exceptions\InvalidRefreshTokenException;
use Symfony\Component\HttpFoundation\Response;

final class AuthBaseController extends BaseController
{
    /**
     * Register a new user
     *
     * Returns an authentication response after registering a new user with the provided registration data and device information.
     *
     * @unauthenticated
     */
    public function register(RegisterRequest $request, RegisterUser $action): AuthResponseResource
    {
        $authData = $action->handle(
            data: $request->toDto(),
        );

        return new AuthResponseResource($authData)->status(Response::HTTP_CREATED);
    }

    /**
     * Login user
     *
     * Returns an authentication response after logging in a user with the provided credentials and device information.
     *
     * @unauthenticated
     *
     * @throws InvalidCredentialsException
     */
    public function login(LoginRequest $request, LoginUser $action): AuthResponseResource
    {
        $authData = $action->handle(
            data: $request->toDto(),
        );

        return new AuthResponseResource($authData);
    }

    /**
     * Refresh tokens
     *
     * Returns a new authentication response with refreshed tokens using the provided refresh token data.
     *
     * @unauthenticated
     *
     * @throws InvalidRefreshTokenException
     */
    public function refresh(RefreshSession $action): AuthResponseResource
    {
        $authData = $action->handle(
            user: auth()->user(),
        );

        return new AuthResponseResource($authData)->status(Response::HTTP_OK);
    }

    /**
     * Logout user
     *
     * Logs out the user by invalidating the session associated with the provided device ID.
     *
     * @unauthenticated
     */
    public function logout(LogoutUser $action): \Illuminate\Http\Response
    {
        $action->handle(
            user: auth()->user(),
        );

        return response()->noContent();
    }

    /**
     * Check authentication status
     *
     * Returns the authentication status and user information for the currently authenticated user.
     */
    public function checkAuth(CheckAuthRequest $request, CheckAuth $action): AuthResponseResource
    {
        $user = auth()->user();
        $authData = $action->handle(
            data: $request->toDto(),
            user: $user,
        );

        $authData->user->load([
            'businesses',
        ]);

        return new AuthResponseResource($authData);
    }
}
