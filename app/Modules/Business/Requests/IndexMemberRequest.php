<?php

declare(strict_types=1);

namespace App\Modules\Business\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Business\Data\IndexMemberData;

class IndexMemberRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'business_id' => ['required', 'integer', 'exists:businesses,id'],
            'filter_query' => ['sometimes', 'nullable', 'string'],
            'sort' => ['sometimes', 'nullable', 'string'],
            'order' => ['sometimes', 'nullable', 'string', 'in:asc,desc'],
            'per_page' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'page' => ['sometimes', 'nullable', 'integer', 'min:1'],
        ];
    }

    public function toDto(): IndexMemberData
    {
        return new IndexMemberData(
            businessId: $this->input('business_id'),
            filterQuery: $this->input('filter_query'),
            sort: $this->input('sort') ?? 'created_at',
            order: $this->input('order') ?? 'asc',
            perPage: (int) ($this->input('per_page') ?? 15),
            page: (int) ($this->input('page') ?? 1),
        );
    }
}
