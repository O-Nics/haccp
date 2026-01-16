<?php

declare(strict_types=1);

namespace App\Features\Auth\Repositories\PasswordReset;

use App\Core\Repositories\BaseRepository;
use App\Models\PasswordResetToken;

class PasswordResetRepository extends BaseRepository implements PasswordResetRepositoryInterface
{
    public function __construct()
    {
        $this->model = PasswordResetToken::class;
    }

    public function findByEmailAndToken(string $email, string $token): ?PasswordResetToken
    {
        return $this->query()
            ->where('email', $email)
            ->where('token', $token)
            ->first();
    }

    public function findLatestByEmail(string $email): ?PasswordResetToken
    {
        return $this->query()
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
        return $this->query()
            ->where('expires_at', '<', now())
            ->delete();
    }

    public function deleteAllByEmail(string $email): int
    {
        return $this->query()
            ->where('email', $email)
            ->delete();
    }
}
