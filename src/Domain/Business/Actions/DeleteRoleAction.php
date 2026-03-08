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
     * Bulk-deletes roles for the given business.
     * Validates all IDs belong to the business in a single query, then bulk hard-deletes.
     *
     * @throws NotFoundException 404 if any role belongs to a different business
     */
    public function handle(DeleteRoleData $deleteRoleData, Business $business): void
    {
        $roles = $this->roleRepository->findByIdsForBusiness($deleteRoleData->roleIds, $business);

        $this->roleEnsure->areAllRolesInBusiness(
            $roles,
            $deleteRoleData->roleIds,
            'Un des rôles spécifiés n\'appartient pas à cette entreprise.',
        );

        $this->roleRepository->deleteMany($roles);
    }
}
