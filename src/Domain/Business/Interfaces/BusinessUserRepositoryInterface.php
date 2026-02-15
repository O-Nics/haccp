<?php

declare(strict_types=1);

namespace Domain\Business\Interfaces;

use Domain\Business\Data\IndexBusinessUserData;
use Domain\Business\Models\Business;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BusinessUserRepositoryInterface
{
    public function getPaginated(Business $business, IndexBusinessUserData $indexBusinessUserData, ?array $relations = []): LengthAwarePaginator;
}
