<?php

declare(strict_types=1);

namespace App\Modules\Business\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Business\Data\IndexMemberData;

final class IndexMemberRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'filter_query' => ['sometimes', 'nullable', 'string'],
            'sort' => ['sometimes', 'nullable', 'string'],
            'order' => ['sometimes', 'nullable', 'string', 'in:asc,desc'],
            'per_page' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'page' => ['sometimes', 'nullable', 'integer', 'min:1'],
        ];
    }

    public function toDto(): IndexMemberData
    {
        $sortable = [
            'first_name' => 'users.first_name',
            'last_name' => 'users.last_name',
            'email' => 'users.email',
            'invited_at' => 'members.invited_at',
            'joined_at' => 'members.joined_at',
            'status' => 'members.status',
            'created_at' => 'members.created_at',
        ];
        return new IndexMemberData(
            filterQuery: $this->input('filter_query'),
            sort: $sortable[$this->input('sort') ?? 'created_at'] ?? 'members.created_at',
            order: $this->input('order') ?? 'asc',
            perPage: (int) ($this->input('per_page') ?? 15),
            page: (int) ($this->input('page') ?? 1),
        );
    }
}
