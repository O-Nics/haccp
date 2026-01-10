<?php

declare(strict_types=1);

namespace App\Features\User\Repositories\User;

use App\Features\Auth\Data\RegisterData;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

readonly class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private User $model
    ) {}

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function store(RegisterData $registerData): User
    {
        $user = new User;
        $user->first_name = $registerData->firstName;
        $user->last_name = $registerData->lastName;
        $user->email = $registerData->email;
        $user->password = Hash::make($registerData->password);
        $user->save();

        return $user;
    }

    public function updateLastLogin(User $user): void
    {
        $user->last_login_at = now();
        $user->save();
    }

    public function updatePassword(User $user, string $password): void
    {
        $user->password = Hash::make($password);
        $user->save();
    }

    public function deleteTokensByDeviceId(User $user, string $deviceId): void
    {
        $user->tokens()->where('name', $deviceId)->delete();
    }
}
