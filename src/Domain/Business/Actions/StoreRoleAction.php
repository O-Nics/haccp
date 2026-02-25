<?php

namespace Domain\Business\Actions;

use App\Core\Actions\BaseAction;
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

    public function handle(StoreRoleData $storeRoleData, Business $business): Role
    {
        // TODO: Implement displayName to Name ( slug )
        // check if the role name already exists in the business
        // create new role
    }
}
