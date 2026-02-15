<?php

declare(strict_types=1);

namespace Domain\Auth\Events;

use App\Core\Events\BaseEvent;
use Domain\Auth\Models\User;

class PasswordResetRequestedEvent extends BaseEvent
{
    public function __construct(
        public User $user,
        public string $token,
    ) {}
}
