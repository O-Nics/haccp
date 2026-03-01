<?php

declare(strict_types=1);

namespace Domain\Business\Notifications;

use App\Core\Notifications\Message\ExtendedMailMessage;
use Domain\Auth\Models\User;
use Domain\Business\Data\MemberInvitedData;
use Domain\Notification\Mails\UserMailNotification;

class MemberInvitedNotification extends UserMailNotification
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
            ->line('Cliquez sur le bouton ci-dessous pour accepter l\'invitation.')
            ->action('Accepter l\'invitation', $acceptInvitationUrl)
            ->line('Si vous n\'avez pas demandé à rejoindre cette entreprise, veuillez ignorer ce message.');
    }
}
