<?php

declare(strict_types=1);

namespace Domain\Business\Events;

use App\Core\Events\BaseEvent;
use Domain\Business\Data\MemberInvitedData;

class MemberInvitedEvent extends BaseEvent
{
    public function __construct(
        public readonly MemberInvitedData $data,
    ) {}
}
