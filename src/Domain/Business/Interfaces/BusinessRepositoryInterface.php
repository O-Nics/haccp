<?php

declare(strict_types=1);

namespace Domain\Business\Interfaces;

use Domain\Auth\Models\User;
use Domain\Business\Data\IndexBusinessData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BusinessRepositoryInterface
{
    public function getUserBusinessPaginated(User $user, IndexBusinessData $indexBusinessData, ?array $relations = []): LengthAwarePaginator;
}
