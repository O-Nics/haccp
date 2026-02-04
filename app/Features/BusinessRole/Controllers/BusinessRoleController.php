<?php

namespace App\Features\BusinessRole\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Resources\BaseAnonymousCollection;
use App\Features\BusinessRole\Repositories\BusinessRoleRepositoryInterface;
use App\Features\BusinessRole\Requests\IndexBusinessRoleRequest;
use App\Features\BusinessRole\Resources\BusinessRoleResource;
use App\Models\Business;

final class BusinessRoleController extends Controller
{
    public function __construct(private readonly BusinessRoleRepositoryInterface $roleRepository) {}

    /**
     * List all roles for a specific business
     *
     * @return BaseAnonymousCollection A JSON response containing a collection of business roles.
     */
    public function index(Business $business, IndexBusinessRoleRequest $request): BaseAnonymousCollection
    {
        $roles = $this->roleRepository->index($business, $request->toDto(), ['permissions', 'users']);

        return BusinessRoleResource::collection($roles);
    }
}
