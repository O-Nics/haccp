<?php

declare(strict_types=1);

namespace Infrastructure\Auth\Repositories;

use Domain\Auth\Interfaces\PasswordResetRepositoryInterface;
use Domain\Auth\Models\PasswordResetToken;

final readonly class PasswordResetRepository implements PasswordResetRepositoryInterface
{
    public function __construct(
        private PasswordResetToken $model
    ) {}

    public function findByEmailAndToken(string $email, string $token): ?PasswordResetToken
    {
        return $this->model->query()
            ->where('email', $email)
            ->where('token', $token)
            ->first();
    }

    public function findLatestByEmail(string $email): ?PasswordResetToken
    {
        return $this->model->query()
            ->where('email', $email)
            ->latest('created_at')
            ->first();
    }

    public function store(string $email, string $token, int $expirationMinutes): PasswordResetToken
    {
        $resetToken = new PasswordResetToken;
        $resetToken->email = $email;
        $resetToken->token = $token;
        $resetToken->expires_at = now()->addMinutes($expirationMinutes);
        $resetToken->save();

        return $resetToken;
    }

    public function markAsConfirmed(PasswordResetToken $resetToken): void
    {
        $resetToken->markAsConfirmed();
        $resetToken->save();
    }

    public function delete(PasswordResetToken $resetToken): bool
    {
        return $resetToken->delete();
    }

    public function deleteExpiredTokens(): int
    {
        return $this->model->query()
            ->where('expires_at', '<', now())
            ->delete();
    }

    public function deleteAllByEmail(string $email): int
    {
        return $this->model->query()
            ->where('email', $email)
            ->delete();
    }
}
