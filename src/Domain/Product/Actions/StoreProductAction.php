<?php

declare(strict_types=1);

namespace Domain\Product\Actions;

use App\Core\Actions\BaseAction;
use Domain\Business\Models\Business;
use Domain\Product\Data\StoreProductData;
use Domain\Product\Interfaces\ProductRepositoryInterface;
use Domain\Product\Models\Product;

final readonly class StoreProductAction extends BaseAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    /**
     * Creates a new product for the given business.
     */
    public function handle(StoreProductData $storeProductData, Business $business): Product
    {
        return $this->productRepository->store($storeProductData, $business);
    }
}
