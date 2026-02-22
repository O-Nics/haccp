<?php

declare(strict_types=1);

namespace Domain\Auth\Listeners;

use App\Core\Listeners\BaseListener;
use Domain\Auth\Events\InvitedMemberEvent;
use Domain\Business\Notifications\InviteMemberNotification;

class SendMemberInvitedNotificationListener extends BaseListener
{
    public function handle(InvitedMemberEvent $event): void
    {
        $event->data->member->user->notify(new InviteMemberNotification($event->data));
    }
}
