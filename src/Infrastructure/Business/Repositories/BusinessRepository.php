<?php

declare(strict_types=1);

namespace Infrastructure\Business\Repositories;

use Domain\Auth\Models\User;
use Domain\Business\Data\IndexBusinessData;
use Domain\Business\Interfaces\BusinessRepositoryInterface;
use Domain\Business\Models\Business;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

final readonly class BusinessRepository implements BusinessRepositoryInterface
{
    public function __construct(private Business $model) {}

    public function indexBusinessPaginated(User $user, IndexBusinessData $indexBusinessData, ?array $relations = []): LengthAwarePaginator
    {
        return $this->model->query()
            ->with($relations)
            ->whereHas('members', function (Builder $query) use ($user) {
                $query->where('members.user_id', $user->id);
            })
            ->filtered($indexBusinessData->filterQuery, ['name'])
            ->order($indexBusinessData->sort, $indexBusinessData->order)
            ->paginate(
                perPage: $indexBusinessData->perPage,
                page: $indexBusinessData->page,
            );
    }

    public function findById(string|int $id, ?array $relations = []): ?Business
    {
        return $this->model->query()
            ->with($relations)
            ->find($id);
    }
}
