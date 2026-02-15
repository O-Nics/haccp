<?php

declare(strict_types=1);

namespace Domain\Auth\Actions;

use App\Core\Exceptions\InvalidCredentialsException;
use Domain\Auth\Data\AuthData;
use Domain\Auth\Data\LoginData;
use Domain\Auth\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

final readonly class LoginUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private CreateAuthSession $createAuthSession,
    ) {}

    /**
     * @throws InvalidCredentialsException
     */
    public function handle(LoginData $data): AuthData
    {
        $user = $this->userRepository->findByEmail($data->email);

        if (! $user || ! Hash::check($data->password, $user->password)) {
            throw new InvalidCredentialsException;
        }

        return $this->createAuthSession->handle($user, $data->deviceData, $data->activeBusiness);
    }
}
