<?php

declare(strict_types=1);

namespace Domain\Business\Actions;

use App\Core\Actions\BaseAction;
use App\Core\Exceptions\NotFoundException;
use Domain\Business\Data\DeleteRoleData;
use Domain\Business\Ensures\RoleEnsure;
use Domain\Business\Interfaces\RoleRepositoryInterface;
use Domain\Business\Models\Business;

final readonly class DeleteRoleAction extends BaseAction
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
        private RoleEnsure $roleEnsure,
    ) {}

    /**
     * Bulk-deletes roles for the given business using a two-pass strategy:
     * first all roles are validated (ownership check), then all are deleted.
     * This ensures no partial deletion occurs if any role fails validation.
     * Non-existent role IDs are silently ignored.
     *
     * @throws NotFoundException 404 if any role belongs to a different business
     */
    public function handle(DeleteRoleData $deleteRoleData, Business $business): void
    {
        $roles = collect();

        foreach ($deleteRoleData->roleIds as $roleId) {
            $role = $this->roleRepository->findById($roleId);
            $this->roleEnsure->itBelongsToBusiness($role, $business, 'Un des rôles spécifiés n\'appartient pas à cetteentreprise.');
            $roles->push($role);
        }

        foreach ($roles as $role) {
            $this->roleRepository->delete($role);
        }
    }
}
