<?php

declare(strict_types=1);

namespace App\Modules\Business\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Resources\BaseAnonymousCollection;
use App\Modules\Business\Requests\IndexBusinessRoleRequest;
use App\Modules\Business\Resources\BusinessRoleResource;
use Domain\Business\Interfaces\BusinessRoleRepositoryInterface;
use Domain\Business\Models\Business;

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
