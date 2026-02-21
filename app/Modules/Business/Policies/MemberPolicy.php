<?php

declare(strict_types=1);

namespace App\Modules\Business\Policies;

use Domain\Auth\Models\User;
use Domain\Business\Enums\PermissionEnum;
use Domain\Business\Models\Business;

final class MemberPolicy
{
    /**
     * Invite a new member to a business.
     * Route: POST /members  →  $this->authorize('invite', [Member::class, $business])
     */
    public function invite(User $user, Business $business): bool
    {
        return $user->memberOf($business)?->hasPermission(PermissionEnum::MEMBERS_INVITE) ?? false;
    }

    /**
     * View members list of a business.
     * Route: GET /members  →  $this->authorize('viewAny', [Member::class, $business])
     */
    public function viewAny(User $user, Business $business): bool
    {
        return $user->memberOf($business)?->hasPermission(PermissionEnum::MEMBERS_VIEW) ?? false;
    }
}
