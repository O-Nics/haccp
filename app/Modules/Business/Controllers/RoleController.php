<?php

declare(strict_types=1);

namespace App\Modules\Business\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Resources\BaseAnonymousCollection;
use App\Modules\Business\Requests\IndexRoleRequest;
use App\Modules\Business\Resources\BusinessRoleResource;
use Domain\Business\Interfaces\RoleRepositoryInterface;

final class RoleController extends BaseController
{
    /**
     * List all roles for a specific business
     */
    public function index(IndexRoleRequest $request, RoleRepositoryInterface $roleRepository): BaseAnonymousCollection
    {
        return BusinessRoleResource::collection($roleRepository->index($request->toDto(), ['permissions', 'members']));
    }
}
