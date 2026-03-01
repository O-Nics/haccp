<?php

declare(strict_types=1);

namespace Domain\Business\Actions;

use App\Core\Actions\BaseAction;
use App\Core\Exceptions\BusinessException;
use App\Core\Exceptions\NotFoundException;
use Domain\Business\Data\UpdateRoleData;
use Domain\Business\Ensures\RoleEnsure;
use Domain\Business\Interfaces\RoleRepositoryInterface;
use Domain\Business\Models\Business;
use Domain\Business\Models\Role;
use Spatie\LaravelData\Optional;

final readonly class UpdateRoleAction extends BaseAction
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
        private RoleEnsure $roleEnsure,
    ) {}

    /**
     * @throws NotFoundException
     * @throws BusinessException
     */
    public function handle(Role $role, UpdateRoleData $updateRoleData, Business $business): Role
    {
        $this->roleEnsure->itBelongsToBusiness($role, $business);
        if (! $updateRoleData->name instanceof Optional) {
            $this->roleEnsure->itDoesNotExist($updateRoleData->name, $business);
        }

        return $this->roleRepository->update($role, $updateRoleData, $business);
    }
}
