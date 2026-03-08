<?php

declare(strict_types=1);

namespace Domain\Business\Enums;

use App\Core\Enums\Enumerations;

enum PermissionEnum: string
{
    use Enumerations;

    // Members
    case MEMBERS_VIEW_ANY = 'members.view-any';
    case MEMBERS_VIEW = 'members.view';
    case MEMBERS_INVITE = 'members.invite';
    case MEMBERS_REMOVE = 'members.remove';
    case MEMBERS_EDIT = 'members.edit';

    // Roles
    case ROLES_VIEW_ANY = 'roles.view-any';
    case ROLES_VIEW = 'roles.view';
    case ROLES_CREATE = 'roles.create';
    case ROLES_EDIT = 'roles.edit';
    case ROLES_DELETE = 'roles.delete';

    // Product
    case PRODUCT_VIEW_ANY = 'product.view-any';
    case PRODUCT_VIEW = 'product.view';
    case PRODUCT_CREATE = 'product.create';
    case PRODUCT_EDIT = 'product.edit';
    case PRODUCT_DELETE = 'product.delete';
}
