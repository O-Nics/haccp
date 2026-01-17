<?php

namespace App\Features\Auth\Enums;

enum TokenAbilityEnum: string
{
    case ACCESS = 'access';
    case REFRESH = 'refresh';
}
