<?php

declare(strict_types=1);

namespace Domain\Auth\Interfaces;

use Domain\Auth\Models\PasswordResetToken;

interface PasswordResetRepositoryInterface
{
    /**
     * Finds a password reset token matching both email and token string. Returns null if not found.
     *
     * @param  string  $email  The email address to search for.
     * @param  string  $token  The plain-text token to match.
     */
    public function findByEmailAndToken(string $email, string $token): ?PasswordResetToken;

    /**
     * Finds the most recently created reset token for the given email. Returns null if none exists.
     *
     * @param  string  $email  The email address to search for.
     */
    public function findLatestByEmail(string $email): ?PasswordResetToken;

    /**
     * Persists a new password reset token for the given email with the specified expiration window.
     *
     * @param  string  $email  The recipient email address.
     * @param  string  $token  The plain-text token to store.
     * @param  int  $expirationMinutes  Number of minutes until the token expires.
     */
    public function store(string $email, string $token, int $expirationMinutes): PasswordResetToken;

    /**
     * Stamps the token as confirmed by setting confirmed_at to the current time.
     *
     * @param  PasswordResetToken  $resetToken  The token to confirm.
     */
    public function markAsConfirmed(PasswordResetToken $resetToken): void;

    /**
     * Hard-deletes the given reset token record.
     *
     * @param  PasswordResetToken  $resetToken  The token to delete.
     */
    public function delete(PasswordResetToken $resetToken): bool;

    /**
     * Hard-deletes all expired tokens. Returns the number of deleted rows.
     */
    public function deleteExpiredTokens(): int;

    /**
     * Hard-deletes all reset tokens associated with the given email. Returns the number of deleted rows.
     *
     * @param  string  $email  The email address whose tokens to delete.
     */
    public function deleteAllByEmail(string $email): int;
}
