<?php

declare(strict_types=1);

namespace Domain\Business\Interfaces;

use Domain\Auth\Models\User;
use Domain\Business\Data\IndexBusinessData;
use Domain\Business\Models\Business;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BusinessRepositoryInterface
{
    /**
     * Finds a business by its primary key with optional eager-loaded relations. Returns null if not found.
     *
     * @param  int|string  $id  The primary key of the business.
     * @param  array|null  $relations  Optional relations to eager load.
     */
    public function findById(int|string $id, ?array $relations = []): ?Business;

    /**
     * Returns a filtered and paginated list of businesses that the authenticated user has access to, with optional eager-loaded relations.
     *
     * @param  User  $user  The authenticated user.
     * @param  IndexBusinessData  $indexBusinessData  Pagination, filtering, and sorting parameters.
     * @param  array|null  $relations  Optional relations to eager load.
     * @return LengthAwarePaginator<Business>
     */
    public function getFilteredPaginated(User $user, IndexBusinessData $indexBusinessData, ?array $relations = []): LengthAwarePaginator;
}
