<?php

declare(strict_types=1);

namespace Infrastructure\Business\Repositories;

use Domain\Auth\Models\User;
use Domain\Business\Data\IndexBusinessData;
use Domain\Business\Interfaces\BusinessRepositoryInterface;
use Domain\Business\Models\Business;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final readonly class BusinessRepository implements BusinessRepositoryInterface
{
    public function __construct(private Business $model) {}

    /**
     * Returns a paginated list of businesses the given user belongs to as a member,
     * with optional filtering, sorting, and eager-loaded relations.
     *
     * @param  User              $user              The authenticated user.
     * @param  IndexBusinessData $indexBusinessData Pagination, filtering, and sorting parameters.
     * @param  array|null        $relations         Optional relations to eager load.
     * @return LengthAwarePaginator<Business>
     */
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

    /**
     * Finds a business by its primary key with optional eager-loaded relations. Returns null if not found.
     *
     * @param  int|string $id        The primary key of the business.
     * @param  array|null $relations Optional relations to eager load.
     * @return Business|null
     */
    public function findById(string|int $id, ?array $relations = []): ?Business
    {
        return $this->model->query()
            ->with($relations)
            ->find($id);
    }
}
