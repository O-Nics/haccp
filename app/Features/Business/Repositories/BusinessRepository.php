<?php

namespace App\Features\Business\Repositories;

use App\Features\User\Data\IndexBusinessData;
use App\Features\User\Data\IndexBusinessUserData;
use App\Models\Business;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

readonly class BusinessRepository implements BusinessRepositoryInterface
{
    public function __construct(private Business $model) {}

    public function getUserBusinessPaginated(User $user, IndexBusinessData $indexBusinessData, ?array $relation = []): LengthAwarePaginator
    {

        return $this->model->query()
            ->whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->with($relation)
            ->filtered($indexBusinessData->filterQuery, ['name'])
            ->order($indexBusinessData->sort, $indexBusinessData->order)
            ->paginate(
                perPage: $indexBusinessData->perPage,
                page: $indexBusinessData->page,
            );
    }

    public function getBusinessUsersPaginated(Business $business, IndexBusinessUserData $indexBusinessUserData, ?array $relation = []): LengthAwarePaginator
    {

        return $business
            ->users()
            ->with($relation)
            ->filtered($indexBusinessUserData->filterQuery, ['first_name', 'last_name', 'email'])
            ->order($indexBusinessUserData->sort, $indexBusinessUserData->order)
            ->paginate(
                perPage: $indexBusinessUserData->perPage,
                page: $indexBusinessUserData->page,
            );
    }
}
