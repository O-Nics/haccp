<?php

declare(strict_types=1);

namespace Domain\Product\Action;

use App\Core\Actions\BaseAction;
use App\Core\Exceptions\BusinessException;
use Domain\Business\Data\StoreRoleData;
use Domain\Business\Ensures\RoleEnsure;
use Domain\Business\Interfaces\RoleRepositoryInterface;
use Domain\Business\Models\Business;
use Domain\Business\Models\Role;
use Domain\Product\Data\StoreProductData;
use Domain\Product\Ensures\ProductEnsure;
use Domain\Product\Interfaces\ProductRepositoryInterface;
use Domain\Product\Models\Product;

final readonly class StoreProductAction extends BaseAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    /**
     * Creates a new product for the given business.
      * Ensures no product with the same name already exists before persisting.
     * @param StoreProductData $storeProductData The data to create the product with.
     * @param Business $business The business the product belongs to.
     * @return Product The created product.
      *
     */
    public function handle(StoreProductData $storeProductData, Business $business): Product
    {
        return $this->productRepository->store($storeProductData, $business);
    }
}
