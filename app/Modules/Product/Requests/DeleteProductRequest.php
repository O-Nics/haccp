<?php

declare(strict_types=1);

namespace App\Modules\Product\Requests;

use App\Core\Requests\BaseFormRequest;
use Domain\Product\Data\DeleteProductData;

final class DeleteProductRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'product_ids' => ['required', 'array'],
            'product_ids.*' => ['string', 'exists:products,id'],
        ];
    }

    public function toDto(): DeleteProductData
    {
        return new DeleteProductData(
            productIds: $this->input('product_ids'),
        );
    }
}
