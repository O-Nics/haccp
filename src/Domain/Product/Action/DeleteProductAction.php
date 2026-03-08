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
use Domain\Product\Data\DeleteProductData;
use Domain\Product\Data\StoreProductData;
use Domain\Product\Data\UpdateProductData;
use Domain\Product\Ensures\ProductEnsure;
use Domain\Product\Interfaces\ProductRepositoryInterface;
use Domain\Product\Models\Product;

final readonly class DeleteProductAction extends BaseAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductEnsure              $productEnsure,
    ) {}

    /**
     * Deletes the specified products from the given business.
     * Ensures all products belong to the specified business before deletion.
     * @param DeleteProductData $deleteProductData The data containing the IDs of products to delete.
     * @param Business $business The business the products belong to.
     * @throws NotFoundException
     */
    public function handle(DeleteProductData $deleteProductData, Business $business): void
    {
        $products = collect();

        foreach ($deleteProductData->productIds as $productId) {
            $product = $this->productRepository->findById($productId);
            $this->productEnsure->isProductInBusiness($product, $business, 'Un des produits spécifiés n\'appartient pas à cetteentreprise.');
            $products->push($product);
        }

        foreach ($products as $product) {
            $this->productRepository->delete($product);
        }
    }
}
