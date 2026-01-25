<?php

namespace App\Features\Role\Repositories\Role;

use App\Features\Role\Data\IndexRoleData;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoleRepositoryInterface
{
    public function index(IndexRoleData $indexRoleData): LengthAwarePaginator;
}
