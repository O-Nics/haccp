<?php

declare(strict_types=1);

namespace Domain\Business\Ensures;

use App\Core\Ensures\BaseEnsure;
use App\Core\Exceptions\BusinessException;
use Domain\Auth\Models\User;
use Domain\Business\Models\Business;

class MemberEnsure extends BaseEnsure
{
    /**
     * Ensures the user is not already a member of the given business.
     *
     * @throws BusinessException 409 if the user is already a member
     */
    public function hasNotAlreadyBeenAddedToBusiness(User $user, Business $business): void
    {
        if ($business->members()->where('user_id', $user->id)->exists()) {
            $this->fail('Cet utilisateur est déjà membre de cette entreprise.');
        }
    }
}
