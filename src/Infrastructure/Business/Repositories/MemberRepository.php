<?php

declare(strict_types=1);

namespace Infrastructure\Business\Repositories;

use App\Core\Repositories\BaseRepository;
use Domain\Business\Data\IndexMemberData;
use Domain\Business\Interfaces\MemberRepositoryInterface;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class MemberRepository extends BaseRepository implements MemberRepositoryInterface
{
    public function __construct(private Member $model) {}

    public function indexPaginated(IndexMemberData $data, Business $business, ?array $relations = []): LengthAwarePaginator
    {
        return $this->model->query()
            ->where('business_id', $business->id)
            ->with($relations)
            ->whereHas('user', function ($query) use ($data) {
                if ($data->filterQuery) {
                    $query->where(function ($q) use ($data) {
                        $q->whereLike('first_name', "%{$data->filterQuery}%")
                            ->orWhereLike('last_name', "%{$data->filterQuery}%")
                            ->orWhereLike('email', "%{$data->filterQuery}%");
                    });
                }
            })
            ->orderBy($data->sort ?? 'created_at', $data->order ?? 'asc')
            ->paginate(
                perPage: $data->perPage,
                page: $data->page,
            );
    }

    public function delete(Member $member): void
    {
        $member->delete();
    }
}
