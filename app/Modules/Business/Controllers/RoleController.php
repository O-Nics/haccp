<?php

declare(strict_types=1);

namespace App\Modules\Business\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Exceptions\BusinessException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Resources\BaseAnonymousCollection;
use App\Modules\Business\Requests\DeleteRoleRequest;
use App\Modules\Business\Requests\IndexRoleRequest;
use App\Modules\Business\Requests\StoreRoleRequest;
use App\Modules\Business\Requests\UpdateRoleRequest;
use App\Modules\Business\Resources\BusinessRoleResource;
use Domain\Business\Actions\DeleteRoleAction;
use Domain\Business\Actions\StoreRoleAction;
use Domain\Business\Actions\UpdateRoleAction;
use Domain\Business\Interfaces\RoleRepositoryInterface;
use Domain\Business\Models\Business;
use Domain\Business\Models\Role;
use Illuminate\Http\Response;

final class RoleController extends BaseController
{
    public function __construct(private readonly RoleRepositoryInterface $roleRepository) {}

    /**
     * Returns a paginated list of roles for the given business.
     */
    public function index(IndexRoleRequest $indexRoleRequest, Business $business): BaseAnonymousCollection
    {
        $rolePaginated = $this->roleRepository->indexPaginated($indexRoleRequest->toDto(), $business);

        return BusinessRoleResource::collection($rolePaginated);
    }

    /**
     * Creates a new role for the given business. Returns 201 on success.
     *
     * @throws BusinessException 409 if a role with the same name already exists
     */
    public function store(StoreRoleRequest $storeRoleRequest, Business $business, StoreRoleAction $storeRoleAction): BusinessRoleResource
    {
        $role = $storeRoleAction->handle($storeRoleRequest->toDto(), $business);

        return new BusinessRoleResource($role)->status(201);
    }

    /**
     * Partially updates a role (PATCH semantics). Returns 200 on success.
     *
     * @throws NotFoundException  404 if the role belongs to a different business
     * @throws BusinessException  409 if the new name conflicts with an existing role
     */
    public function update(UpdateRoleRequest $updateRoleRequest, Business $business, Role $role, UpdateRoleAction $updateRoleAction): BusinessRoleResource
    {
        $role = $updateRoleAction->handle($role, $updateRoleRequest->toDto(), $business);

        return new BusinessRoleResource($role);
    }

    /**
     * Bulk-deletes roles by ID for the given business. Returns 204 on success.
     *
     * @throws NotFoundException 404 if any role belongs to a different business
     */
    public function delete(DeleteRoleRequest $deleteRoleRequest, Business $business, DeleteRoleAction $deleteRoleAction): Response
    {
        $deleteRoleAction->handle($deleteRoleRequest->toDto(), $business);

        return response()->noContent();
    }
}
