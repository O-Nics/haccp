<?php

declare(strict_types=1);

namespace Domain\Business\Ensures;

use App\Core\Ensures\BaseEnsure;
use App\Core\Exceptions\BusinessException;
use App\Core\Exceptions\NotFoundException;
use Domain\Business\Models\Business;
use Domain\Business\Models\Role;

class RoleEnsure extends BaseEnsure
{
    /**
     * @throws BusinessException
     */
    public function itDoesNotExist(string $roleName, Business $business): void
    {
        if ($business->roles()->where('name', $roleName)->exists()) {
            $this->fail('Ce rôle existe déjà dans cette entreprise.');
        }
    }

    /**
     * @throws BusinessException
     */
    public function hasNoAssignedMembers(Role $role, Business $business): void
    {
        if ($role->members()->where('business_id', $business->id)->exists()) {
            $this->fail('Ce rôle ne peut pas être supprimé car il est assigné à des membres.');
        }
    }

    /**
     * @throws NotFoundException
     */
    public function itBelongsToBusiness(Role $role, Business $business): void
    {
        if ($role->business_id !== $business->id) {
            $this->failNotFound('Ce rôle n\'appartient pas à cette entreprise.');
        }
    }
}