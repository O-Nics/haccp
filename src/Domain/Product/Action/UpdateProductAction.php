<?php

declare(strict_types=1);

namespace Domain\Product\Action;

use App\Core\Actions\BaseAction;
use App\Core\Exceptions\BusinessException;
use App\Core\Exceptions\NotFoundException;
use Domain\Business\Data\StoreRoleData;
use Domain\Business\Ensures\RoleEnsure;
use Domain\Business\Interfaces\RoleRepositoryInterface;
use Domain\Business\Models\Business;
use Domain\Business\Models\Role;
use Domain\Product\Data\StoreProductData;
use Domain\Product\Data\UpdateProductData;
use Domain\Product\Ensures\ProductEnsure;
use Domain\Product\Interfaces\ProductRepositoryInterface;
use Domain\Product\Models\Product;

final readonly class UpdateProductAction extends BaseAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductEnsure              $productEnsure,
    ) {}

    /**
     * Updates the given product with the provided data.
     * Ensures the product belongs to the specified business before updating.
     *
     * @param UpdateProductData $updateProductData The data to update the product with.
     * @param Business $business The business the product belongs to.
     * @param Product $product The product to update.
     * @return Product The updated product.
     * @throws NotFoundException
     */
    public function handle(UpdateProductData $updateProductData, Business $business, Product $product): Product
    {
        $this->productEnsure->isProductInBusiness($product, $business);
        return $this->productRepository->update($updateProductData, $product);
    }
}
