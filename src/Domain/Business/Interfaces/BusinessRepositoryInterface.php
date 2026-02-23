<?php

declare(strict_types=1);

namespace Domain\Business\Interfaces;

use Domain\Auth\Models\User;
use Domain\Business\Data\IndexBusinessData;
use Domain\Business\Models\Business;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BusinessRepositoryInterface
{
    public function findById(int|string $id, ?array $relations = []): ?Business;

    public function indexBusinessPaginated(User $user, IndexBusinessData $indexBusinessData, ?array $relations = []): LengthAwarePaginator;
}
