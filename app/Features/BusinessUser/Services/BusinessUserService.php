<?php

declare(strict_types=1);

namespace App\Features\BusinessUser\Services;

use App\Features\BusinessUser\Data\InviteUserData;
use App\Features\User\Repositories\User\UserRepositoryInterface;
use App\Models\Business;
use Domain\Auth\Data\StoreUserData;
use Domain\Auth\Models\User;
use Illuminate\Support\Str;

final readonly class BusinessUserService
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    public function inviteUserToBusiness(Business $business, InviteUserData $userData): User
    {
        $user = $this->userRepository->findByEmail($userData->email);

        if ($user) {
            if (! $business->users->contains($user->id)) {
                $business->users()->attach($user->id, [
                    'invited_by' => auth()->id(),
                    'invited_at' => now(),
                ]);
                $business->save();
            }

            return $user;
        }

        $generatedPassword = Str::random(12);

        $registerData = new StoreUserData(
            firstName: $userData->first_name,
            lastName: $userData->last_name,
            email: $userData->email,
            password: $generatedPassword,
        );

        $user = $this->userRepository->store($registerData);

        $business->users()->attach($user->id, [
            'invited_by' => auth()->id(),
            'invited_at' => now(),
        ]);
        $business->save();

        return $user;
    }
}
