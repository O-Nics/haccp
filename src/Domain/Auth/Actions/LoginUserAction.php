<?php

declare(strict_types=1);

namespace Domain\Auth\Actions;

use App\Core\Actions\BaseAction;
use Domain\Auth\Data\AuthData;
use Domain\Auth\Data\LoginData;
use Domain\Auth\Exceptions\InvalidCredentialsException;
use Domain\Auth\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

final readonly class LoginUserAction extends BaseAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private CreateAuthSessionAction $createAuthSession,
    ) {}

    /**
     * @throws InvalidCredentialsException
     */
    public function handle(LoginData $loginData): AuthData
    {
        $user = $this->userRepository->findByEmail($loginData->email);

        if (! $user || ! Hash::check($loginData->password, $user->password)) {
            throw new InvalidCredentialsException;
        }

        return $this->createAuthSession->handle($user, $loginData->deviceData, $loginData->activeBusiness);
    }
}
