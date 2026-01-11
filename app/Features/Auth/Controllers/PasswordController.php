<?php

declare(strict_types=1);

namespace App\Features\Auth\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Exceptions\InvalidCredentialsException;
use App\Features\Auth\Requests\CheckResetPasswordResetRequest;
use App\Features\Auth\Requests\ForgotPasswordRequest;
use App\Features\Auth\Requests\ResetPasswordRequest;
use App\Features\Auth\Services\PasswordService;
use Illuminate\Http\JsonResponse;

/**
 * PasswordController handles password reset operations
 */
final class PasswordController extends Controller
{
    public function __construct(
        private readonly PasswordService $passwordService
    ) {}

    /**
     * Send password reset link
     */
    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {
        $data = $request->toDto();

        $this->passwordService->sendResetLink($data);

        return apiSuccess();
    }

    /**
     * Confirm password reset token
     *
     * @throws InvalidCredentialsException
     */
    public function checkReset(CheckResetPasswordResetRequest $request): JsonResponse
    {
        $data = $request->toDto();

        $this->passwordService->confirmResetToken($data);

        return apiSuccess();
    }

    /**
     * Reset password
     *
     * @throws InvalidCredentialsException
     */
    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $data = $request->toDto();

        $this->passwordService->resetPassword($data);

        return apiSuccess();
    }
}
