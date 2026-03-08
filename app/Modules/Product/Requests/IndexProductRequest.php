<?php

declare(strict_types=1);

namespace App\Modules\Product\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Product\Data\IndexProductData;

final class IndexProductRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'filter_query' => ['nullable', 'string'],
            'sort' => ['nullable', 'string', 'in:name,created_at,updated_at'],
            'order' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function toDto(): IndexProductData
    {
        return new IndexProductData(
            filterQuery: $this->input('filter_query'),
            sort: $this->input('sort') ?? 'created_at',
            order: $this->input('order') ?? 'asc',
            perPage: (int) ($this->input('per_page') ?? 15),
            page: (int) ($this->input('page') ?? 1),
        );
    }
}
