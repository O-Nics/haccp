<?php

declare(strict_types=1);

namespace Domain\Auth\Notifications;

use Domain\Auth\Models\User;
use Domain\Notification\Mails\UserMailNotification;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordResetConfirmedNotification extends UserMailNotification
{
    protected function mailMessage(User $user, MailMessage $message): MailMessage
    {
        return $message
            ->subject('Password Reset Successful')
            ->line('Your password has been successfully reset.')
            ->line('If you did not perform this action, please contact support immediately.');
    }
}
