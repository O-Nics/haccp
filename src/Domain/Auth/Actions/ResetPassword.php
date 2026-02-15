<?php

declare(strict_types=1);

namespace Domain\Auth\Actions;

use App\Core\Exceptions\InvalidCredentialsException;
use Domain\Auth\Data\ResetPasswordData;
use Domain\Auth\Interfaces\PasswordResetRepositoryInterface;
use Domain\Auth\Interfaces\UserRepositoryInterface;
use Domain\Auth\Mail\PasswordResetConfirmedMail;
use Illuminate\Support\Facades\Mail;

final readonly class ResetPassword
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordResetRepositoryInterface $passwordResetRepository,
    ) {}

    /**
     * @throws InvalidCredentialsException
     */
    public function handle(ResetPasswordData $data): void
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

        $this->userRepository->updatePassword($user, $data->password);

        $this->passwordResetRepository->deleteAllByEmail($data->email);

        Mail::to($user->email)->queue(new PasswordResetConfirmedMail($user));
        Mail::to($data->email)->queue(new PasswordResetConfirmedMail($user));
    }
}
