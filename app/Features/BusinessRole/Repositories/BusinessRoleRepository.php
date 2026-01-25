<?php

namespace App\Features\BusinessRole\Repositories;

use App\Features\BusinessRole\Data\IndexBusinessRoleData;
use App\Models\Business;
use App\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;

class BusinessRoleRepository implements BusinessRoleRepositoryInterface
{
    public function index(Business $business, IndexBusinessRoleData $indexRoleData, ?array $relation = []): LengthAwarePaginator
    {
        return Role::query()
            ->with($relation)
            ->where('business_id', $business->id)
            ->filtered($indexRoleData->filterQuery, ['display_name', 'description'])
            ->order($indexRoleData->sort, $indexRoleData->order)
            ->paginate(
                perPage: $indexRoleData->perPage,
                page: $indexRoleData->page,
            );
    }
}
