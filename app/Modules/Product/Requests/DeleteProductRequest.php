<?php

declare(strict_types=1);

namespace App\Modules\Product\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Product\Data\DeleteProductData;
use Domain\Product\Data\StoreProductData;

final class DeleteProductRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'products' => ['required', 'array'],
            'products.*.id' => ['required', 'string', 'exists:products,id'],
        ];
    }

    public function toDto(): DeleteProductData
    {
        return new DeleteProductData(
            productIds: collect($this->input('products'))->pluck('id')->toArray(),
        );
    }
}
