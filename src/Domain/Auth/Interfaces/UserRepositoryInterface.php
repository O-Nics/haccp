<?php

declare(strict_types=1);

namespace Domain\Auth\Interfaces;

use Domain\Auth\Data\StoreUserData;
use Domain\Auth\Models\User;

interface UserRepositoryInterface
{
    /**
     * Finds a user by their email address. Returns null if not found.
     *
     * @param  string  $email  The email address to search for.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Persists a new user from the given data and returns the created instance.
     *
     * @param  StoreUserData  $registerData  The data used to create the user.
     */
    public function store(StoreUserData $registerData): User;

    /**
     * Updates the last_login_at timestamp for the given user to the current time.
     *
     * @param  User  $user  The user to update.
     */
    public function updateLastLogin(User $user): void;

    /**
     * Hashes and stores a new password for the given user.
     *
     * @param  User  $user  The user whose password to update.
     * @param  string  $password  The new plain-text password to hash and store.
     */
    public function updatePassword(User $user, string $password): void;
}
