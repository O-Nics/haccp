<?php

declare(strict_types=1);

namespace Domain\Notification\Mails;

use App\Core\Notifications\BaseNotification;
use Domain\Auth\Models\User;
use Domain\Notification\Channels\MailChannel;
use Illuminate\Notifications\Messages\MailMessage;

abstract class UserMailNotification extends BaseNotification
{
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
