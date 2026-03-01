<?php

declare(strict_types=1);

namespace Infrastructure\Auth\Repositories;

use App\Core\Repositories\BaseRepository;
use Domain\Auth\Interfaces\PasswordResetRepositoryInterface;
use Domain\Auth\Models\PasswordResetToken;

final readonly class PasswordResetRepository extends BaseRepository implements PasswordResetRepositoryInterface
{
    public function __construct(
        private PasswordResetToken $model
    ) {}

    /**
     * Finds a password reset token matching both email and token string. Returns null if not found.
     *
     * @param  string  $email  The email address to search for.
     * @param  string  $token  The plain-text token to match.
     */
    public function findByEmailAndToken(string $email, string $token): ?PasswordResetToken
    {
        return $this->model->query()
            ->where('email', $email)
            ->where('token', $token)
            ->first();
    }

    /**
     * Finds the most recently created reset token for the given email. Returns null if none exists.
     *
     * @param  string  $email  The email address to search for.
     */
    public function findLatestByEmail(string $email): ?PasswordResetToken
    {
        return $this->model->query()
            ->where('email', $email)
            ->latest('created_at')
            ->first();
    }

    /**
     * Persists a new password reset token for the given email with the specified expiration window.
     *
     * @param  string  $email  The recipient email address.
     * @param  string  $token  The plain-text token to store.
     * @param  int  $expirationMinutes  Number of minutes until the token expires.
     */
    public function store(string $email, string $token, int $expirationMinutes): PasswordResetToken
    {
        $resetToken = $this->model->newInstance();
        $resetToken->email = $email;
        $resetToken->token = $token;
        $resetToken->expires_at = now()->addMinutes($expirationMinutes);
        $resetToken->save();

        return $resetToken;
    }

    /**
     * Stamps the token as confirmed by setting confirmed_at to the current time.
     *
     * @param  PasswordResetToken  $resetToken  The token to confirm.
     */
    public function markAsConfirmed(PasswordResetToken $resetToken): void
    {
        $resetToken->markAsConfirmed();
        $resetToken->save();
    }

    /**
     * Hard-deletes the given reset token record.
     *
     * @param  PasswordResetToken  $resetToken  The token to delete.
     */
    public function delete(PasswordResetToken $resetToken): bool
    {
        return $resetToken->delete();
    }

    /**
     * Hard-deletes all expired tokens. Returns the number of deleted rows.
     */
    public function deleteExpiredTokens(): int
    {
        return $this->model->query()
            ->where('expires_at', '<', now())
            ->delete();
    }

    /**
     * Hard-deletes all reset tokens associated with the given email. Returns the number of deleted rows.
     *
     * @param  string  $email  The email address whose tokens to delete.
     */
    public function deleteAllByEmail(string $email): int
    {
        return $this->model->query()
            ->where('email', $email)
            ->delete();
    }
}
