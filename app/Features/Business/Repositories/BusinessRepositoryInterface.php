<?php

namespace App\Features\Business\Repositories;

use App\Features\User\Data\IndexBusinessData;
use App\Features\User\Data\IndexBusinessUserData;
use App\Models\Business;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BusinessRepositoryInterface
{
    public function getUserBusinessPaginated(User $user, IndexBusinessData $indexBusinessData, ?array $relation = []): LengthAwarePaginator;
    public function getBusinessUsersPaginated(Business $business, IndexBusinessUserData $indexBusinessUserData, ?array $relation = []): LengthAwarePaginator;
}
