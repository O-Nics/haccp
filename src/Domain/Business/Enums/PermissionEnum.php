<?php

declare(strict_types=1);

namespace Domain\Business\Enums;

enum PermissionEnum: string
{
    // Members
    case MEMBERS_VIEW_ANY = 'members.view-any';
    case MEMBERS_VIEW = 'members.view';
    case MEMBERS_INVITE = 'members.invite';
    case MEMBERS_REMOVE = 'members.remove';
    case MEMBERS_EDIT = 'members.edit';

    // Roles
    case ROLES_VIEW = 'roles.view';
    case ROLES_CREATE = 'roles.create';
    case ROLES_EDIT = 'roles.edit';
    case ROLES_DELETE = 'roles.delete';
}
