<?php

declare(strict_types=1);

namespace Domain\Notification\Mails;

use App\Core\Notifications\BaseNotification;
use App\Core\Notifications\Message\ExtendedMailMessage;
use Domain\Auth\Models\User;
use Domain\Notification\Channels\MailChannel;

abstract class UserMailNotification extends BaseNotification
{
    public function via(User $notifiable): array
    {
        return [MailChannel::class];
    }

    public function toMail(User $notifiable): ExtendedMailMessage
    {
        $message = (new ExtendedMailMessage)
            ->greeting('Bonjour '.$notifiable->first_name.',')
            ->salutation("L'équipe Haccp");

        return $this->mailMessage($notifiable, $message);
    }

    abstract protected function mailMessage(User $user, ExtendedMailMessage $message): ExtendedMailMessage;

    public function toArray(User $notifiable): array
    {
        return [
            'user_id' => $notifiable->id,
        ];
    }
}
