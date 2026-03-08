<?php

declare(strict_types=1);

namespace App\Modules\Product\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Product\Data\StoreProductData;

final class StoreProductRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function toDto(): StoreProductData
    {
        return new StoreProductData(
            name: $this->input('name'),
        );
    }
}
