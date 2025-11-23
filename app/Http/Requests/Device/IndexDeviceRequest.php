<?php

declare(strict_types=1);

namespace App\Http\Requests\Device;

use App\DataTransferObjects\Device\IndexDeviceData;
use App\Http\Requests\Base\BaseFormRequest;

class IndexDeviceRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filterQuery' => ['sometimes', 'nullable', 'string'],
            'sort' => ['sometimes', 'nullable', 'string'],
            'order' => ['sometimes', 'nullable', 'string', 'in:asc,desc'],
            'perPage' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'nullable', 'integer', 'min:1'],
        ];
    }

    public function toDto(): IndexDeviceData
    {
        return new IndexDeviceData(
            filterQuery: $this->input('filterQuery'),
            sort: $this->input('sort', 'last_used_at'),
            order: $this->input('order', 'desc'),
            perPage: $this->input('perPage', 20),
            page: $this->input('page', 1),
        );
    }
}
