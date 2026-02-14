<?php

namespace Domain\Auth\Enums;

enum TokenAbilityEnum: string
{
    case ACCESS = 'access';
    case REFRESH = 'refresh';
}
