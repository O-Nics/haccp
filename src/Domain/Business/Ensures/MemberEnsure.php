<?php

declare(strict_types=1);

namespace Domain\Business\Ensures;

use App\Core\Ensures\BaseEnsure;
use App\Core\Exceptions\BusinessException;
use Domain\Auth\Models\User;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;

class MemberEnsure extends BaseEnsure
{
    /**
     * @throws BusinessException
     */
    public function hasNotAlreadyBeenAddedToBusiness(User $user, Business $business): void
    {
        if (Member::where('business_id', $business->id)->where('user_id', $user->id)->exists()) {
            $this->fail('Cet utilisateur est déjà membre de cette entreprise.');
        }
    }
}
