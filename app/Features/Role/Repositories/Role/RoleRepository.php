<?php

namespace App\Features\Role\Repositories\Role;

use App\Features\Role\Data\IndexRoleData;
use App\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleRepository implements RoleRepositoryInterface
{
    public function index(IndexRoleData $indexRoleData, ?array $relation = []): LengthAwarePaginator
    {
        return Role::query()
            ->with($relation)
            ->where('business_id', $indexRoleData->businessId)
            ->filtered($indexRoleData->filterQuery, ['display_name'])
            ->order($indexRoleData->sort, $indexRoleData->order)
            ->paginate(
                perPage: $indexRoleData->perPage,
                page: $indexRoleData->page,
            );
    }

}
