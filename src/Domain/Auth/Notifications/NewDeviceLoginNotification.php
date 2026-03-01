<?php

declare(strict_types=1);

namespace Domain\Auth\Notifications;

use App\Core\Notifications\Message\ExtendedMailMessage;
use Domain\Auth\Models\Device;
use Domain\Auth\Models\User;
use Domain\Notification\Mails\UserMailNotification;

class NewDeviceLoginNotification extends UserMailNotification
{
    public function __construct(
        public readonly Device $device,
    ) {}

    protected function mailMessage(User $user, ExtendedMailMessage $message): ExtendedMailMessage
    {
        return $message
            ->subject('New Device Login Detected')
            ->line('A new device was used to log into your account.')
            ->line('**Device:** '.($this->device->model ?? 'Unknown'))
            ->line('**Platform:** '.$this->device->platform)
            ->line('**IP Address:** '.$this->device->ip_address)
            ->line('If this was not you, please secure your account immediately.');
    }
}
