<?php

declare(strict_types=1);

namespace Domain\Business\Enums;

use App\Core\Enums\Enumerations;

enum RoleNameEnum: string
{
    use Enumerations;

    case OWNER    = 'owner';
    case MANAGER  = 'manager';
    case EMPLOYEE = 'employee';
    case VIEWER   = 'viewer';
}
