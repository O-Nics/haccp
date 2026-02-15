<?php

declare(strict_types=1);

namespace Infrastructure\Business\Repositories;

use Domain\Auth\Models\User;
use Domain\Business\Data\IndexBusinessData;
use Domain\Business\Interfaces\BusinessRepositoryInterface;
use Domain\Business\Models\Business;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class BusinessRepository implements BusinessRepositoryInterface
{
    public function __construct(private Business $model) {}

    public function getUserBusinessPaginated(User $user, IndexBusinessData $indexBusinessData, ?array $relations = []): LengthAwarePaginator
    {
        return $this->model->query()
            ->whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->with($relations)
            ->filtered($indexBusinessData->filterQuery, ['name'])
            ->order($indexBusinessData->sort, $indexBusinessData->order)
            ->paginate(
                perPage: $indexBusinessData->perPage,
                page: $indexBusinessData->page,
            );
    }
}
