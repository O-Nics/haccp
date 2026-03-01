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
     * Ensures no role with the given slug name already exists in the business.
     *
     * @param  string   $roleName The slug name to check for uniqueness.
     * @param  Business $business The business to check within.
     * @throws BusinessException 409 if a role with the same name exists
     */
    public function itDoesNotExist(string $roleName, Business $business): void
    {
        if ($business->roles()->where('name', $roleName)->exists()) {
            $this->fail('Ce rôle existe déjà dans cette entreprise.');
        }
    }

    /**
     * Ensures the role belongs to the given business.
     * Returns 404 instead of 403 to avoid leaking resource existence across tenants.
     *
     * @param  Role         $role          The role to verify.
     * @param  Business     $business      The expected owner business.
     * @param  string|null  $customMessage Optional message to override the default error.
     * @throws NotFoundException 404 if the role belongs to a different business
     */
    public function itBelongsToBusiness(Role $role, Business $business, ?string $customMessage = null): void
    {
        if ($role->business_id !== $business->id) {
            $this->failNotFound($customMessage ?? 'Ce rôle n\'appartient pas à cette entreprise.');
        }
    }
}
