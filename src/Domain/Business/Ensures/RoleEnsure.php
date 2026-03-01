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
     * @throws NotFoundException
     */
    public function itBelongsToBusiness(Role $role, Business $business, ?string $customMessage = null): void
    {
        if ($role->business_id !== $business->id) {
            $this->failNotFound($customMessage ?? 'Ce rôle n\'appartient pas à cette entreprise.');
        }
    }
}
