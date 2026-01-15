<?php

namespace App\Features\Auth\Notifications;

use App\Features\Notifications\Mails\UserMailNotification;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;

class UserRegisterNotification extends UserMailNotification
{
    public function __construct(
    ) {}

    protected function mailMessage(User $user, MailMessage $message): MailMessage
    {
        return $message
            ->subject('Bienvenue sur Liberlo ✨')
            ->greeting("Bienvenue {$user->first_name},")
            ->line("Toute l'équipe Haccp vous souhaite la bienvenue. Nous sommes ravis de vous accueillir.")
            ->action('Accéder à mon espace client', 'https://app.liberlo.com/login');
    }
}
