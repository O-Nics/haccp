<?php

declare(strict_types=1);

namespace App\Modules\Business\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Business\Data\IndexRoleData;

final class IndexRoleRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'filter.query' => ['sometimes', 'string', 'max:255'],
            'sort' => ['sometimes', 'string', 'in:created_at,display_name,description'],
            'order' => ['sometimes', 'string', 'in:asc,desc'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }

    public function toDto(): IndexRoleData
    {
        return new IndexRoleData(
            filterQuery: $this->input('filter_query'),
            sort: $this->input('sort') ?? 'created_at',
            order: $this->input('order'),
            perPage: (int) $this->input('per_page', 15),
            page: (int) $this->input('page', 1),
        );
    }
}
