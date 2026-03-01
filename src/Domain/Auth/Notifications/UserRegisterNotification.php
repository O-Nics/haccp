<?php

declare(strict_types=1);

namespace Domain\Auth\Notifications;

use App\Core\Notifications\Message\ExtendedMailMessage;
use Domain\Auth\Models\User;
use Domain\Notification\Mails\UserMailNotification;

class UserRegisterNotification extends UserMailNotification
{
    protected function mailMessage(User $user, ExtendedMailMessage $message): ExtendedMailMessage
    {
        return $message
            ->subject('Bienvenue sur Haccp')
            ->greeting("Bienvenue {$user->first_name},")
            ->line("Toute l'équipe Haccp vous souhaite la bienvenue. Nous sommes ravis de vous accueillir.");
    }
}
