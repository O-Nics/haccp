<?php

declare(strict_types=1);

namespace App\Modules\Business\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Resources\BaseAnonymousCollection;
use App\Modules\Business\Requests\IndexRoleRequest;
use App\Modules\Business\Resources\BusinessRoleResource;
use Domain\Business\Interfaces\RoleRepositoryInterface;
use Domain\Business\Models\Business;

final class RoleController extends BaseController
{
    /**
     * List all roles for a specific business
     */
    public function index(IndexRoleRequest $request, Business $business, RoleRepositoryInterface $roleRepository): BaseAnonymousCollection
    {
        $rolePaginated = $roleRepository->indexPaginated($request->toDto(), $business);
        return BusinessRoleResource::collection($rolePaginated);
    }
}
