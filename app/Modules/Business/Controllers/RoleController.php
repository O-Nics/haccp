<?php

declare(strict_types=1);

namespace App\Modules\Business\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Exceptions\BusinessException;
use App\Core\Resources\BaseAnonymousCollection;
use App\Modules\Business\Requests\IndexRoleRequest;
use App\Modules\Business\Requests\StoreRoleRequest;
use App\Modules\Business\Requests\UpdateRoleRequest;
use App\Modules\Business\Resources\BusinessRoleResource;
use Domain\Business\Actions\StoreRoleAction;
use Domain\Business\Actions\UpdateRoleAction;
use Domain\Business\Interfaces\RoleRepositoryInterface;
use Domain\Business\Models\Business;
use Domain\Business\Models\Role;

final class RoleController extends BaseController
{
    /**
     * List all roles for a specific business
     */
    public function __construct(private RoleRepositoryInterface $roleRepository) {}

    public function index(IndexRoleRequest $indexRoleRequest, Business $business): BaseAnonymousCollection
    {
        $rolePaginated = $this->roleRepository->indexPaginated($indexRoleRequest->toDto(), $business);

        return BusinessRoleResource::collection($rolePaginated);
    }

    /**
     * @throws BusinessException
     */
    public function store(StoreRoleRequest $storeRoleRequest, Business $business,  StoreRoleAction $storeRoleAction): BusinessRoleResource
    {
        $role = $storeRoleAction->handle($storeRoleRequest->toDto(), $business);

        return new BusinessRoleResource($role)->status(201);
    }

    public function update(UpdateRoleRequest $updateRoleRequest, Business $business, Role $role,  UpdateRoleAction $updateRoleAction): BusinessRoleResource
    {
        $role = $updateRoleAction->handle($role, $updateRoleRequest->toDto(), $business);

        return new BusinessRoleResource($role);
    }
}
