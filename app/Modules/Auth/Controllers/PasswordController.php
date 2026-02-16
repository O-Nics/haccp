<?php

declare(strict_types=1);

namespace App\Modules\Auth\Controllers;

use App\Core\Controllers\BaseController;
use App\Modules\Auth\Requests\CheckResetPasswordResetRequest;
use App\Modules\Auth\Requests\ForgotPasswordRequest;
use App\Modules\Auth\Requests\ResetPasswordRequest;
use Domain\Auth\Actions\ConfirmPasswordResetAction;
use Domain\Auth\Actions\ResetPasswordAction;
use Domain\Auth\Actions\SendPasswordResetLinkAction;
use Domain\Auth\Exceptions\InvalidCredentialsException;
use Illuminate\Http\Response;

/**
 * PasswordController handles password reset operations
 */
final class PasswordController extends BaseController
{
    /**
     * Send password reset link
     */
    public function forgot(ForgotPasswordRequest $request, SendPasswordResetLinkAction $action): Response
    {
        $action->handle($request->toDto());

        return response()->noContent();
    }

    /**
     * Confirm password reset token
     *
     * @throws InvalidCredentialsException
     */
    public function checkReset(CheckResetPasswordResetRequest $request, ConfirmPasswordResetAction $action): Response
    {
        $action->handle($request->toDto());

        return response()->noContent();
    }

    /**
     * Reset password
     *
     * @throws InvalidCredentialsException
     */
    public function reset(ResetPasswordRequest $request, ResetPasswordAction $action): Response
    {
        $action->handle($request->toDto());

        return response()->noContent();
    }
}
