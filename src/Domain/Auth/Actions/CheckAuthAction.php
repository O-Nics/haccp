<?php

declare(strict_types=1);

namespace Domain\Auth\Actions;

use App\Core\Actions\BaseAction;
use Domain\Auth\Data\AuthData;
use Domain\Auth\Data\CheckAuthData;
use Domain\Auth\Models\User;

final readonly class CheckAuthAction extends BaseAction
{
    public function __construct(
        private CreateAuthSessionAction $createAuthSession,
    ) {}

    public function handle(CheckAuthData $data, User $user): AuthData
    {
        return $this->createAuthSession->handle($user, $data->deviceInfo, $data->activeBusinessId);
    }
}
