<?php

declare(strict_types=1);

namespace Domain\Notification\Channels;

use Domain\Notification\Models\NotificationSent;

abstract class NotificationChannelState
{
    abstract public function resend(NotificationSent $notificationSent): void;
}
