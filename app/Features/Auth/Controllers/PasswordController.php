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

        return response()->json([
            'message' => 'Password reset link sent to your email',
        ]);
    }

    /**
     * Confirm password reset token
     */
    public function checkReset(CheckResetPasswordResetRequest $request): JsonResponse
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
