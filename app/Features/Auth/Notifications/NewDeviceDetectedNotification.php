<?php

namespace App\Features\Auth\Notifications;

use App\Features\Notifications\Mails\UserMailNotification;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;

class NewDeviceDetectedNotification extends UserMailNotification
{
    public function __construct(
    ) {}

    protected function mailMessage(User $user, MailMessage $message): MailMessage
    {
        return $message
            ->subject('Bienvenue sur Haccp ✨')
            ->greeting("Bienvenue {$user->first_name},")
            ->line("Toute l'équipe Haccp vous souhaite la bienvenue. Nous sommes ravis de vous accueillir.");
    }
}
