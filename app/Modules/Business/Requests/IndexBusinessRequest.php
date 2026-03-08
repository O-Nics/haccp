<?php

declare(strict_types=1);

namespace App\Modules\Business\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Business\Data\IndexBusinessData;

final class IndexBusinessRequest extends BaseFormRequest
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

    public function toDto(): IndexBusinessData
    {
        return new IndexBusinessData(
            filterQuery: $this->filter_query,
            sort: $this->sort ?? 'name',
            order: $this->order ?? 'asc',
            perPage: (int) ($this->per_page ?? 15),
            page: (int) ($this->page ?? 1),
        );
    }
}
