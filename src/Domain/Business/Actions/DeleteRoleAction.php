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
     * @throws NotFoundException
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
            $this->roleRepository->delete($role, $business);
        }
    }
}
