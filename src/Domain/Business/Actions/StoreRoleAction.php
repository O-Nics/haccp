<?php

declare(strict_types=1);

namespace Domain\Business\Actions;

use App\Core\Actions\BaseAction;
use App\Core\Exceptions\BusinessException;
use Domain\Business\Data\StoreRoleData;
use Domain\Business\Ensures\RoleEnsure;
use Domain\Business\Interfaces\RoleRepositoryInterface;
use Domain\Business\Models\Business;
use Domain\Business\Models\Role;

final readonly class StoreRoleAction extends BaseAction
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
        private RoleEnsure $roleEnsure,
    ) {}

    /**
     * @throws BusinessException
     */
    public function handle(StoreRoleData $storeRoleData, Business $business): Role
    {
        $this->roleEnsure->itDoesNotExist($storeRoleData->name, $business);

        return $this->roleRepository->store($storeRoleData, $business);
    }
}
