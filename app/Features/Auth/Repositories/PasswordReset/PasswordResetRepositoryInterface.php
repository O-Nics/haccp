<?php

declare(strict_types=1);

namespace App\Features\Auth\Repositories\PasswordReset;

use Domain\Auth\Models\PasswordResetToken;

interface PasswordResetRepositoryInterface
{
    public function findByEmailAndToken(string $email, string $token): ?PasswordResetToken;

    public function findLatestByEmail(string $email): ?PasswordResetToken;

    public function store(string $email, string $token, int $expirationMinutes): PasswordResetToken;

    public function markAsConfirmed(PasswordResetToken $resetToken): void;

    public function delete(PasswordResetToken $resetToken): bool;

    public function deleteExpiredTokens(): int;

    public function deleteAllByEmail(string $email): int;
}
