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

    public function findByEmail(string $email): ?User
    {
        return $this->model->query()->where('email', $email)->first();
    }

    public function store(StoreUserData $registerData): User
    {
        $user = $this->model->newInstance();
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
}
