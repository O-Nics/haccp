<?php

declare(strict_types=1);

namespace Domain\Business\Enums;

enum MemberStatusEnum: string
{
    // Members
    case ACTIVE = 'active';
    case INVITED = 'invited';

    case REMOVED = 'removed';

}
