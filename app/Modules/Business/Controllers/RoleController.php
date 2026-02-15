<?php

declare(strict_types=1);

namespace App\Modules\Business\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Resources\BaseAnonymousCollection;
use App\Modules\Business\Requests\IndexRoleRequest;
use App\Modules\Business\Resources\BusinessRoleResource;
use Domain\Business\Interfaces\BusinessRepositoryInterface;
use Domain\Business\Interfaces\BusinessRoleRepositoryInterface;

final class RoleController extends BaseController
{
    public function __construct(private readonly BusinessRoleRepositoryInterface $roleRepository) {}

    /**
     * List all roles for a specific business
     *
     * @return BaseAnonymousCollection A JSON response containing a collection of business roles.
     */
    public function index(IndexRoleRequest $request): BaseAnonymousCollection
    {
        $roles = $this->roleRepository->index($request->toDto(), ['permissions', 'users']);

        return BusinessRoleResource::collection($roles);
    }
}
