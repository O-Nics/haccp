<?php

declare(strict_types=1);

namespace Domain\Business\Ensures;

use App\Core\Ensures\BaseEnsure;
use App\Core\Exceptions\BusinessException;
use App\Core\Exceptions\NotFoundException;
use Domain\Auth\Models\User;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;

class MemberEnsure extends BaseEnsure
{
    /**
     * Ensures the user is not already a member of the given business.
     *
     * @param  User     $user     The user to check.
     * @param  Business $business The business to check membership against.
     * @throws BusinessException 409 if the user is already a member
     */
    public function hasNotAlreadyBeenAddedToBusiness(User $user, Business $business): void
    {
        if ($business->members()->where('user_id', $user->id)->exists()) {
            $this->fail('Cet utilisateur est déjà membre de cette entreprise.');
        }
    }

    /**
     * Ensures the member being deleted is not the owner of the business organisation.
     *
     * @param  Member   $member   The member to check.
     * @param  Business $business The business the member belongs to.
     * @throws BusinessException 409 if the member is the organisation owner
     */
    public function isNotTheOwner(Member $member, Business $business): void
    {
        if ($member->business->organisation->owner_id === $member->user_id) {
            $this->fail('Vous ne pouvez pas supprimer le propriétaire de l\'entreprise.');
        }
    }

    /**
     * Ensures the member belongs to the given business.
     * Returns 404 instead of 403 to avoid leaking resource existence across tenants.
     *
     * @param  Member   $member   The member to verify.
     * @param  Business $business The business to check membership against.
     * @throws NotFoundException 404 if the member does not belong to the business
     */
    public function isMemberOfBusiness(Member $member, Business $business): void
    {
        if (! $business->members()->where('id', $member->id)->exists()) {
            $this->failNotFound('Cette entreprise n\'a pas de membre avec cet utilisateur.');
        }
    }
}
