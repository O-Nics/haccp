<?php

declare(strict_types=1);

namespace Domain\Business\Enums;

use App\Core\Enums\Enumerations;

enum MemberStatusEnum: string
{
    use Enumerations;

    // Members
    case ACTIVE = 'active';
    case INVITED = 'invited';

    case REMOVED = 'removed';

}
