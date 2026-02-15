<?php

declare(strict_types=1);

namespace App\Modules\Auth\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Exceptions\InvalidCredentialsException;
use App\Modules\Auth\Requests\CheckResetPasswordResetRequest;
use App\Modules\Auth\Requests\ForgotPasswordRequest;
use App\Modules\Auth\Requests\ResetPasswordRequest;
use Domain\Auth\Actions\ConfirmPasswordReset;
use Domain\Auth\Actions\ResetPassword;
use Domain\Auth\Actions\SendPasswordResetLink;
use Illuminate\Http\Response;

/**
 * PasswordController handles password reset operations
 */
final class PasswordController extends Controller
{
    /**
     * Send password reset link
     */
    public function forgot(ForgotPasswordRequest $request, SendPasswordResetLink $action): Response
    {
        $action->handle($request->toDto());

        return response()->noContent();
    }

    /**
     * Confirm password reset token
     *
     * @throws InvalidCredentialsException
     */
    public function checkReset(CheckResetPasswordResetRequest $request, ConfirmPasswordReset $action): Response
    {
        $action->handle($request->toDto());

        return response()->noContent();
    }

    /**
     * Reset password
     *
     * @throws InvalidCredentialsException
     */
    public function reset(ResetPasswordRequest $request, ResetPassword $action): Response
    {
        $action->handle($request->toDto());

        return response()->noContent();
    }
}
