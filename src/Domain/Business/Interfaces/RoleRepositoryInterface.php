<?php

declare(strict_types=1);

namespace Domain\Business\Interfaces;

use Domain\Business\Data\IndexRoleData;
use Domain\Business\Data\StoreRoleData;
use Domain\Business\Data\UpdateRoleData;
use Domain\Business\Models\Business;
use Domain\Business\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoleRepositoryInterface
{
    /**
     * @param IndexRoleData $indexRoleData
     * @param Business $business
     * @param array|null $relation
     * @return LengthAwarePaginator<Role>
     *
     * This method retrieves a paginated list of roles for a specific business, with optional eager loading of related models.
     */
    public function indexPaginated(IndexRoleData $indexRoleData, Business $business, ?array $relation = []): LengthAwarePaginator;

    /**
     * @param StoreRoleData $storeRoleData
     * @param Business $business
     * @return Role
     *
     * This method creates a new role for a specific business using the provided data.
     */
    public function store(StoreRoleData $storeRoleData, Business $business): Role;

    /**
     * @param Role $role
     * @param UpdateRoleData $updateRoleData
     * @param Business $business
     * @return Role
     *
     * This method updates an existing role for a specific business using the provided data.
     */
    public function update(Role $role, UpdateRoleData $updateRoleData, Business $business): Role;
}
