<?php

declare(strict_types=1);

namespace Domain\Auth\Notifications;

use App\Core\Notifications\Message\ExtendedMailMessage;
use Domain\Auth\Models\User;
use Domain\Notification\Mails\UserMailNotification;

class PasswordResetConfirmedNotification extends UserMailNotification
{
    protected function mailMessage(User $user, ExtendedMailMessage $message): ExtendedMailMessage
    {
        return $message
            ->subject('Password Reset Successful')
            ->line('Your password has been successfully reset.')
            ->line('If you did not perform this action, please contact support immediately.');
    }
}
