<?php

declare(strict_types=1);

namespace Domain\Auth\Enums;

enum TokenAbilityEnum: string
{
    case ACCESS = 'access';
    case REFRESH = 'refresh';
}
