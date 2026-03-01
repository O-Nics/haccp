<?php

declare(strict_types=1);

namespace Domain\Auth\Enums;

use App\Core\Enums\Enumerations;

enum TokenAbilityEnum: string
{
    use Enumerations;

    case ACCESS = 'access';
    case REFRESH = 'refresh';
}
