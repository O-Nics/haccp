<?php

declare(strict_types=1);

namespace Domain\Business\Listeners;

use App\Core\Listeners\BaseListener;
use Domain\Business\Events\MemberInvitedEvent;
use Domain\Business\Notifications\MemberInvitedNotification;
use Domain\Business\Notifications\MemberInvitedWithPasswordNotification;

class SendMemberInvitationListener extends BaseListener
{
    public function handle(MemberInvitedEvent $event): void
    {
        $user = $event->data->member->user;
        if ($event->data->temporaryPassword !== null) {
            $user->notify(new MemberInvitedWithPasswordNotification($event->data));
        } else {
            $user->notify(new MemberInvitedNotification($event->data));
        }
    }
}
