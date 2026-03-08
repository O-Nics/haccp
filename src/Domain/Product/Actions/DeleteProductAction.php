<?php

declare(strict_types=1);

namespace Domain\Product\Actions;

use App\Core\Actions\BaseAction;
use App\Core\Exceptions\NotFoundException;
use Domain\Business\Models\Business;
use Domain\Product\Data\DeleteProductData;
use Domain\Product\Ensures\ProductEnsure;
use Domain\Product\Interfaces\ProductRepositoryInterface;

final readonly class DeleteProductAction extends BaseAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductEnsure              $productEnsure,
    ) {}

    /**
     * Deletes the specified products from the given business.
     * Validates all IDs belong to the business in a single query, then bulk soft-deletes.
     *
     * @param  DeleteProductData  $deleteProductData  The data containing the IDs of products to delete.
     * @param  Business  $business  The business the products belong to.
     * @throws NotFoundException
     */
    public function handle(DeleteProductData $deleteProductData, Business $business): void
    {
        $products = $this->productRepository->findByIdsForBusiness($deleteProductData->productIds, $business);

        $this->productEnsure->areAllProductsInBusiness(
            $products,
            $deleteProductData->productIds,
            'Un des produits spécifiés n\'appartient pas à cette entreprise.',
        );

        $this->productRepository->deleteMany($products);
    }
}
