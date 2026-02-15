<?php

namespace Domain\Notification\Mails;

use Domain\Auth\Models\User;
use Domain\Notification\Channels\MailChannel;
use Domain\Notification\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

abstract class UserMailNotification extends BaseNotification
{
    use Queueable;

    public function via(User $notifiable): array
    {
        return [MailChannel::class];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->greeting('Bonjour '.$notifiable->first_name.',')
            ->salutation("L'équipe Haccp");

        return $this->mailMessage($notifiable, $message);
    }

    abstract protected function mailMessage(User $user, MailMessage $message): MailMessage;

    public function toArray(User $notifiable): array
    {
        return [
            'user_id' => $notifiable->id,
        ];
    }
}
