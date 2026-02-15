<?php

declare(strict_types=1);

namespace Domain\Auth\Actions;

use Domain\Auth\Interfaces\SessionRepositoryInterface;
use Domain\Auth\Models\User;

final readonly class LogoutUser
{
    public function __construct(
        private SessionRepositoryInterface $sessionRepository,
    ) {}

    public function handle(User $user): void
    {
        $session = $user->currentSession();

        if ($session) {
            $this->sessionRepository->deleteSession($session);
        }
    }
}
