<?php

declare(strict_types=1);

namespace App\Modules\Auth\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Exceptions\InvalidCredentialsException;
use App\Features\Auth\Services\PasswordService;
use App\Modules\Auth\Requests\CheckResetPasswordResetRequest;
use App\Modules\Auth\Requests\ForgotPasswordRequest;
use App\Modules\Auth\Requests\ResetPasswordRequest;
use Illuminate\Http\Response;

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
    public function forgot(ForgotPasswordRequest $request): Response
    {
        $data = $request->toDto();

        $this->passwordService->sendResetLink($data);

        return response()->noContent();
    }

    /**
     * Confirm password reset token
     *
     * @throws InvalidCredentialsException
     */
    public function checkReset(CheckResetPasswordResetRequest $request): Response
    {
        $data = $request->toDto();

        $this->passwordService->confirmResetToken($data);

        return response()->noContent();
    }

    /**
     * Reset password
     *
     * @throws InvalidCredentialsException
     */
    public function reset(ResetPasswordRequest $request): Response
    {
        $data = $request->toDto();

        $this->passwordService->resetPassword($data);

        return response()->noContent();
    }
}
