<?php

namespace App\Features\Role\Controllers;

use App\Core\Controllers\Controller;
use App\Core\Resources\BaseAnonymousCollection;
use App\Features\Role\Repositories\Role\RoleRepositoryInterface;
use App\Features\Role\Requests\IndexRoleRequest;
use App\Features\User\Resources\RoleResource;

final class RoleController extends Controller
{
    public function __construct(private readonly RoleRepositoryInterface $roleRepository){}

    public function index(IndexRoleRequest $request): BaseAnonymousCollection
    {
        $roles = $this->roleRepository->index($request->toDto(), ['permissions']);

        return RoleResource::collection($roles);
    }

}
