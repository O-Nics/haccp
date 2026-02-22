<?php

declare(strict_types=1);

namespace Domain\Business\Notifications;

use Domain\Auth\Data\MemberInvitedData;
use Domain\Auth\Models\User;
use Domain\Notification\Mails\UserMailNotification;
use Illuminate\Notifications\Messages\MailMessage;

class InviteMemberNotification extends UserMailNotification
{
    public function __construct(
        public readonly MemberInvitedData $data,
    ) {}
    protected function mailMessage(User $user, MailMessage $message): MailMessage
    {
        return $message
           ->subject('Vous avez été invité à rejoindre '.$this->data->member->business->name)
           ->line('Vous avez été invité à rejoindre l\'entreprise '.$this->data->member->business->name.'.')
           ->line('Cliquez sur le bouton ci-dessous pour accepter l\'invitation et rejoindre l\'entreprise.')
           ->action('Accepter l\'invitation', route('businesses.members.accept-invitation', ['member' => $this->data->member->id]))
           ->line('Si vous n\'avez pas demandé à rejoindre cette entreprise, veuillez ignorer ce message.')
           ->line($this->data->temporaryPassword ? 'Votre mot de passe temporaire est : ' : '')
           ->line($this->data->temporaryPassword ?? '');
    }
}
