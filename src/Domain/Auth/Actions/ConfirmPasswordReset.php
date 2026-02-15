<?php

declare(strict_types=1);

namespace Domain\Auth\Actions;

use Domain\Auth\Data\ConfirmPasswordResetData;
use Domain\Auth\Exceptions\InvalidCredentialsException;
use Domain\Auth\Interfaces\PasswordResetRepositoryInterface;

final readonly class ConfirmPasswordReset
{
    public function __construct(
        private PasswordResetRepositoryInterface $passwordResetRepository,
    ) {}

    /**
     * @throws InvalidCredentialsException
     */
    public function handle(ConfirmPasswordResetData $data): void
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

        $this->passwordResetRepository->markAsConfirmed($resetToken);
    }
}
