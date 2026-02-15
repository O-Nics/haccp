<?php

declare(strict_types=1);

namespace App\Modules\Business\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Business\Data\IndexBusinessUserData;
use Domain\Business\Models\Business;

class IndexBusinessUserRequest extends BaseFormRequest
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

    public function toDto(): IndexBusinessUserData
    {
        return new IndexBusinessUserData(
            businessId: $this->input('business_id'),
            filterQuery: $this->filter_query,
            sort: $this->sort ?? 'first_name',
            order: $this->order ?? 'asc',
            perPage: (int) ($this->per_page ?? 15),
            page: (int) ($this->page ?? 1),
        );
    }
}
