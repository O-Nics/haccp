<?php

namespace App\Features\Notifications\Mails;

use App\Core\Channels\MailChannel;
use App\Core\Notifications\BaseNotification;
use App\Models\User;
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
