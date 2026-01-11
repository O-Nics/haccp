<?php

declare(strict_types=1);

namespace App\Features\User\Repositories\User;

use App\Features\Auth\Data\StoreUserData;
use App\Models\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function store(StoreUserData $registerData): User;

    public function updateLastLogin(User $user): void;

    public function updatePassword(User $user, string $password): void;

    public function deleteTokensByDeviceId(User $user, string $deviceId): void;
}
