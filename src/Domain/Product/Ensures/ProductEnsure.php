<?php

declare(strict_types=1);

namespace Domain\Product\Ensures;

use App\Core\Ensures\BaseEnsure;
use App\Core\Exceptions\BusinessException;
use App\Core\Exceptions\NotFoundException;
use Domain\Business\Models\Business;
use Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Collection;

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

    /**
     * Ensures all requested product IDs were found within the business.
     * Throws a 404 if any ID is missing or belongs to a different business.
     *
     * @param  Collection<int, Product>  $foundProducts
     * @param  array  $requestedIds
     * @throws NotFoundException
     */
    public function areAllProductsInBusiness(Collection $foundProducts, array $requestedIds, ?string $customMessage = null): void
    {
        if ($foundProducts->count() !== count($requestedIds)) {
            $this->failNotFound($customMessage ?? 'Un des produits spécifiés n\'appartient pas à cette entreprise.');
        }
    }
}
