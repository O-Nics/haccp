<?php

declare(strict_types=1);

namespace Domain\Business\Notifications;

use App\Core\Notifications\Message\ExtendedMailMessage;
use Domain\Auth\Models\User;
use Domain\Business\Data\MemberInvitedData;
use Domain\Notification\Mails\UserMailNotification;

class MemberInvitedWithPasswordNotification extends UserMailNotification
{
    public function __construct(
        public readonly MemberInvitedData $data,
    ) {}

    protected function mailMessage(User $user, ExtendedMailMessage $message): ExtendedMailMessage
    {
        $acceptInvitationUrl = "http://localhost:3000/businesses/members/accept-invitation?member={$this->data->member->id}";

        return $message
            ->subject('Vous avez été invité à rejoindre '.$this->data->member->business->name)
            ->line('Vous avez été invité à rejoindre l\'entreprise '.$this->data->member->business->name.'.')
            ->line('Un compte a été créé pour vous. Votre mot de passe temporaire est :')
            ->box($this->data->temporaryPassword)
            ->line('Cliquez sur le bouton ci-dessous pour accepter l\'invitation et vous connecter.')
            ->action('Accepter l\'invitation', $acceptInvitationUrl)
            ->line('Nous vous recommandons de changer votre mot de passe après votre première connexion.')
            ->line('Si vous n\'avez pas demandé à rejoindre cette entreprise, veuillez ignorer ce message.');
    }
}
