<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\InvalidCredentialsException;
use App\Http\Controllers\Base\Controller;
use App\Http\Requests\Password\ConfirmPasswordResetRequest;
use App\Http\Requests\Password\ForgotPasswordRequest;
use App\Http\Requests\Password\ResetPasswordRequest;
use App\Services\Password\PasswordService;
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

        return response()->json([
            'message' => 'Password reset link sent to your email',
        ]);
    }

    /**
     * Confirm password reset token
     */
    public function checkReset(ConfirmPasswordResetRequest $request): JsonResponse
    {
        $data = $request->toDto();

        try {
            $this->passwordService->confirmResetToken($data);

            return response()->json([
                'message' => 'Token confirmed successfully. You may now reset your password.',
            ]);
        } catch (InvalidCredentialsException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Reset password
     */
    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $data = $request->toDto();

        try {
            $this->passwordService->resetPassword($data);

            return response()->json([
                'message' => 'Password reset successfully',
            ]);
        } catch (InvalidCredentialsException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
