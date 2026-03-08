<?php

declare(strict_types=1);

namespace Domain\Business\Interfaces;

use Domain\Business\Data\IndexRoleData;
use Domain\Business\Data\StoreRoleData;
use Domain\Business\Data\UpdateRoleData;
use Domain\Business\Models\Business;
use Domain\Business\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoleRepositoryInterface
{
    /**
     * Returns a paginated list of roles for a specific business, with optional filtering and eager loading of related models.
     *
     * @param  IndexRoleData  $indexRoleData  Pagination, filtering, and sorting parameters.
     * @param  Business  $business  The business to list roles for.
     * @param  array|null  $relation  Optional relations to eager load.
     * @return LengthAwarePaginator<Role>
     */
    public function getFilteredPaginated(IndexRoleData $indexRoleData, Business $business, ?array $relation = []): LengthAwarePaginator;

    /**
     * Persists a new role for the given business and returns the created instance.
     *
     * @param  StoreRoleData  $storeRoleData  The data used to create the role.
     * @param  Business  $business  The business the role belongs to.
     */
    public function store(StoreRoleData $storeRoleData, Business $business): Role;

    /**
     * Partially updates a role. Only non-Optional fields are applied (PATCH semantics).
     * Permissions are synced via pivot table when provided.
     *
     * @param  Role  $role  The role to update.
     * @param  UpdateRoleData  $updateRoleData  The partial update data.
     * @param  Business  $business  The owning business.
     */
    public function update(Role $role, UpdateRoleData $updateRoleData, Business $business): Role;

    /**
     * Finds a role by its primary key. Returns null if not found.
     *
     * @param  int|string  $roleId  The primary key of the role.
     */
    public function findById(int|string $roleId): ?Role;

    /**
     * Hard-deletes the given role from the database.
     *
     * @param  Role  $role  The role to delete.
     */
    public function delete(Role $role): void;

    /**
     * Retrieves all roles matching the given IDs that belong to the specified business.
     *
     * @param  array  $ids  The list of role IDs to retrieve.
     * @param  Business  $business  The business the roles must belong to.
     * @return Collection<int, Role>
     */
    public function findByIdsForBusiness(array $ids, Business $business): Collection;

    /**
     * Hard-deletes all roles in the given collection in a single query.
     *
     * @param  Collection<int, Role>  $roles  The roles to delete.
     */
    public function deleteMany(Collection $roles): void;
}
