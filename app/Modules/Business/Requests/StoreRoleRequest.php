<?php

declare(strict_types=1);

namespace App\Modules\Business\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Business\Data\IndexRoleData;

class StoreRoleRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['required', 'array'],
            'permissions.*.id' => ['required', 'string', 'exists:permissions,id'],
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
