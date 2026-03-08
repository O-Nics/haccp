<?php

declare(strict_types=1);

namespace Domain\Product\Ensures;

use App\Core\Ensures\BaseEnsure;
use App\Core\Exceptions\BusinessException;
use App\Core\Exceptions\NotFoundException;
use Domain\Auth\Models\User;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Domain\Product\Models\Product;

class ProductEnsure extends BaseEnsure
{
    /**
     * @throws NotFoundException
     */
    public function isProductInBusiness(Product $product, Business $business, ?string $customMessage = null): void
    {
        if (! $business->products()->where('id', $product->id)->exists()) {
            $this->failNotFound($customMessage ?? 'Le produit n\'existe pas dans cette entreprise.');
        }
    }
}
