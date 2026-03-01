<?php

declare(strict_types=1);

namespace Infrastructure\Auth\Repositories;

use App\Core\Repositories\BaseRepository;
use Domain\Auth\Data\StoreUserData;
use Domain\Auth\Interfaces\UserRepositoryInterface;
use Domain\Auth\Models\User;
use Illuminate\Support\Facades\Hash;

final readonly class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(
        private User $model
    ) {}

    /**
     * Finds a user by their email address. Returns null if not found.
     *
     * @param  string  $email  The email address to search for.
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->query()->where('email', $email)->first();
    }

    /**
     * Persists a new user from the given data and returns the created instance.
     *
     * @param  StoreUserData  $registerData  The data used to create the user.
     */
    public function store(StoreUserData $registerData): User
    {
        $user = $this->model->newInstance();
        $user->first_name = $registerData->firstName;
        $user->last_name = $registerData->lastName;
        $user->email = $registerData->email;
        $user->password = Hash::make($registerData->password);
        $user->temporary_password = $registerData->temporaryPassword;
        $user->save();

        return $user;
    }

    /**
     * Updates the last_login_at timestamp for the given user to the current time.
     *
     * @param  User  $user  The user to update.
     */
    public function updateLastLogin(User $user): void
    {
        $user->last_login_at = now();
        $user->save();
    }

    /**
     * Hashes and stores a new password for the given user.
     *
     * @param  User  $user  The user whose password to update.
     * @param  string  $password  The new plain-text password to hash and store.
     */
    public function updatePassword(User $user, string $password): void
    {
        $user->password = Hash::make($password);
        $user->save();
    }
}
