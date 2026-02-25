<?php

declare(strict_types=1);

namespace Domain\Business\Ensures;

use App\Core\Ensures\BaseEnsure;
use App\Core\Exceptions\BusinessException;
use Domain\Auth\Models\User;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;

class RoleEnsure extends BaseEnsure
{
    /**
     * @throws BusinessException
     */
    public function isRoleDoesNotExist(string $roleName, Business $business): void
    {
        if ($business->roles()->where('name', $roleName)->exists()) {
            $this->fail('Ce rôle existe déjà dans cette entreprise.');
        }
    }


}
