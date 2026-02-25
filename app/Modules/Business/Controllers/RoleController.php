<?php

declare(strict_types=1);

namespace App\Modules\Business\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Resources\BaseAnonymousCollection;
use App\Modules\Business\Requests\IndexRoleRequest;
use App\Modules\Business\Requests\StoreRoleRequest;
use App\Modules\Business\Resources\BusinessRoleResource;
use Domain\Business\Interfaces\RoleRepositoryInterface;
use Domain\Business\Models\Business;

final class RoleController extends BaseController
{
    /**
     * List all roles for a specific business
     */
    public function __construct(private RoleRepositoryInterface $roleRepository)
    {

    }
    public function index(IndexRoleRequest $request, Business $business): BaseAnonymousCollection
    {
        $rolePaginated = $this->roleRepository->indexPaginated($request->toDto(), $business);

        return BusinessRoleResource::collection($rolePaginated);
    }

    public function store(StoreRoleRequest $request, Business $business): BusinessRoleResource
    {
        // faire une action pour créer un role et lui assigner des permissions
        $role = $this->roleRepository->store($request->toDto(), $business);

        return new BusinessRoleResource($role)->status(201);
    }
}
