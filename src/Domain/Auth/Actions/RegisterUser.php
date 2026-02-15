<?php

declare(strict_types=1);

namespace Domain\Auth\Actions;

use App\Core\Actions\BaseAction;
use Domain\Auth\Data\AuthData;
use Domain\Auth\Data\RegisterData;
use Domain\Auth\Events\UserRegisteredEvent;
use Domain\Auth\Interfaces\UserRepositoryInterface;

final readonly class RegisterUser extends BaseAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private CreateAuthSession $createAuthSession,
    ) {}

    public function handle(RegisterData $data): AuthData
    {
        $user = $this->userRepository->store($data->storeUserData);

        event(new UserRegisteredEvent($user));

        return $this->createAuthSession->handle($user, $data->deviceInfo);
    }
}
