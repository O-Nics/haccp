<?php

declare(strict_types=1);

namespace App\Features\Business\Repositories;

use App\Features\Business\Data\IndexBusinessData;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BusinessRepositoryInterface
{
    public function getUserBusinessPaginated(User $user, IndexBusinessData $indexBusinessData, ?array $relations = []): LengthAwarePaginator;
}
