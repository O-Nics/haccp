<?php

declare(strict_types=1);

namespace Domain\Business\Policies;

use App\Core\Policy\BasePolicy;
use Domain\Auth\Models\User;
use Domain\Business\Enums\PermissionEnum;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Illuminate\Auth\Access\Response;

final class MemberPolicy extends BasePolicy
{
    public function viewAny(User $user, Business $business): Response
    {
        return $this->hasPermission($user, $business, PermissionEnum::MEMBERS_VIEW_ANY);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Business $business): Response
    {
        return $this->hasPermission($user, $business, PermissionEnum::MEMBERS_VIEW);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Business $business): Response
    {
        return $this->hasPermission($user, $business, PermissionEnum::MEMBERS_INVITE);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Business $business): Response
    {
        return $this->hasPermission($user, $business, PermissionEnum::MEMBERS_EDIT);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Business $business,Member $member): Response
    {
        if ($user->id === $member->user_id) {
            return $this->deny('Vous ne pouvez pas vous supprimer vous même.');
        }
        return $this->hasPermission($user, $business, PermissionEnum::MEMBERS_REMOVE);
    }
}
