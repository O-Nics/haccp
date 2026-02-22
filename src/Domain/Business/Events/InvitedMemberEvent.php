<?php

declare(strict_types=1);

namespace Domain\Business\Events;

use App\Core\Events\BaseEvent;
use Domain\Auth\Data\MemberInvitedData;
use Domain\Auth\Models\User;

class InvitedMemberEvent extends BaseEvent
{
    public function __construct(
        public readonly MemberInvitedData $data,
    ) {}
}
