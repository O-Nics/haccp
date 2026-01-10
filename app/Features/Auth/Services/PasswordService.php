<?php

declare(strict_types=1);

namespace App\Features\Auth\Services;

use App\Exceptions\InvalidCredentialsException;
use App\Features\Auth\Data\ConfirmPasswordResetData;
use App\Features\Auth\Data\ForgotPasswordData;
use App\Features\Auth\Data\ResetPasswordData;
use App\Mail\Password\PasswordResetConfirmedMail;
use App\Mail\Password\PasswordResetMail;
use App\Models\PasswordResetToken;
use App\Repositories\PasswordReset\PasswordResetRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Mail;

final readonly class PasswordService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordResetRepositoryInterface $passwordResetRepository,
    ) {}

    /**
     * Send password reset email
     */
    public function sendResetLink(ForgotPasswordData $data): void
    {
        $user = $this->userRepository->findByEmail($data->email);

        if (! $user) {
            // Don't reveal if email exists or not (security)
            return;
        }

        // Delete old tokens for this email
        $this->passwordResetRepository->deleteAllByEmail($data->email);

        // Create new reset token
        $token = PasswordResetToken::generateToken();
        $expirationMinutes = config('auth.password_reset.expiration', 60);

        $resetToken = $this->passwordResetRepository->store(
            $data->email,
            $token,
            $expirationMinutes
        );

        // Send email
        Mail::to($user->email)->queue(new PasswordResetMail($user, $token));
    }

    /**
     * Confirm password reset token (marks token as confirmed)
     *
     * @throws InvalidCredentialsException
     */
    public function confirmResetToken(ConfirmPasswordResetData $data): void
    {
        $resetToken = $this->passwordResetRepository->findByEmailAndToken($data->email, $data->token);

        if (! $resetToken) {
            throw new InvalidCredentialsException('Invalid reset token', 400);
        }

        if ($resetToken->isExpired()) {
            throw new InvalidCredentialsException('Reset token has expired', 400);
        }

        if ($resetToken->isConfirmed()) {
            throw new InvalidCredentialsException('Reset token already used', 400);
        }

        // Mark as confirmed
        $this->passwordResetRepository->markAsConfirmed($resetToken);
    }

    /**
     * Reset password with confirmed token
     *
     * @throws InvalidCredentialsException
     */
    public function resetPassword(ResetPasswordData $data): void
    {
        $resetToken = $this->passwordResetRepository->findByEmailAndToken($data->email, $data->token);

        if (! $resetToken) {
            throw new InvalidCredentialsException('Invalid reset token', 400);
        }

        if ($resetToken->isExpired()) {
            throw new InvalidCredentialsException('Reset token has expired', 400);
        }

        if (! $resetToken->isConfirmed()) {
            throw new InvalidCredentialsException('Reset token must be confirmed first', 400);
        }

        $user = $this->userRepository->findByEmail($data->email);

        if (! $user) {
            throw new InvalidCredentialsException('User not found', 400);
        }

        // Update password
        $this->userRepository->updatePassword($user, $data->password);

        // Delete all reset tokens for this email
        $this->passwordResetRepository->deleteAllByEmail($data->email);

        // Send confirmation email
        Mail::to($user->email)->queue(new PasswordResetConfirmedMail($user));
        Mail::to($data->email)->queue(new PasswordResetConfirmedMail($user));
    }

    /**
     * Clean up expired tokens (can be called via scheduled job)
     */
    public function cleanupExpiredTokens(): int
    {
        return $this->passwordResetRepository->deleteExpiredTokens();
    }
}
