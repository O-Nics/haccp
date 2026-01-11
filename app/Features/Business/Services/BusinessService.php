<?php

namespace App\Features\Business\Services;

use App\Features\Business\Data\RegisterUserData;
use App\Features\Business\Repositories\BusinessRepositoryInterface;
use App\Features\User\Data\InviteUserData;
use App\Features\User\Repositories\User\UserRepositoryInterface;
use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Str;

readonly class BusinessService
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {

    }

    public function inviteUserToBusiness(Business $business, InviteUserData $userData): User
    {
        $user = $this->userRepository->findByEmail($userData->email);

        if ($user) {
            if (!$business->users->contains($user->id)) {
                $business->users()->attach($user->id, [
                    'invited_by' => auth()->id(),
                    'invited_at' => now(),
                ]);
                $business->save();
            }
            return $user;
        }

        $generatedPassword = Str::random(12);

        $registerData = new RegisterUserData(
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
