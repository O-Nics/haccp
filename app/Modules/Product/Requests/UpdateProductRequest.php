<?php

declare(strict_types=1);

namespace App\Modules\Product\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Product\Data\UpdateProductData;

final class UpdateProductRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function toDto(): UpdateProductData
    {
        return new UpdateProductData(
            name: $this->value('name'),
        );
    }
}
