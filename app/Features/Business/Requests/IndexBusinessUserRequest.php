<?php

// app/Http/Requests/RefreshTokenRequest.php

declare(strict_types=1);

namespace App\Features\Business\Requests;

use App\Core\Requests\BaseFormRequest;
use App\Features\User\Data\IndexBusinessUserData;

class IndexBusinessUserRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'filter_query' => ['sometimes', 'nullable', 'string'],
            'sort' => ['sometimes', 'nullable', 'string'],
            'order' => ['sometimes', 'nullable', 'string', 'in:asc,desc'],
            'per_page' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'nullable', 'integer', 'min:1'],
        ];
    }

    public function toDto(): IndexBusinessUserData
    {
        return new IndexBusinessUserData(
            filterQuery: $this->filter_query,
            sort: $this->sort ?? 'created_at',
            order: $this->order ?? 'asc',
            perPage: $this->per_page ?? 2,
            page: $this->page ?? 1,
        );
    }
}
