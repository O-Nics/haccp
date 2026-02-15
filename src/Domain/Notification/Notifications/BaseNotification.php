<?php

declare(strict_types=1);

namespace Domain\Notification\Notifications;

use App\Core\Notifications\BaseNotification as CoreBaseNotification;
use Domain\Auth\Models\User;

class BaseNotification extends CoreBaseNotification
{
    public function mustSend(User $notifiable): bool
    {
        return true;
    }

    public function isConfidential(): bool
    {
        return false;
    }
}
