<?php

declare(strict_types=1);

namespace Domain\Product\Interfaces;

use Domain\Business\Models\Business;
use Domain\Product\Data\IndexProductData;
use Domain\Product\Data\StoreProductData;
use Domain\Product\Data\UpdateProductData;
use Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{

        /**
        * Retrieves a paginated list of products based on the provided filter and pagination data.
        *
        * @param IndexProductData $indexProductData The data containing filter and pagination information.
        * @param Business $business The business to which the products belong.
        * @param array|null $relations Optional relations to eager load with the products.
        * @return LengthAwarePaginator A paginated collection of products matching the criteria.
        */
    public function getFilteredPaginated(IndexProductData $indexProductData, Business $business, ?array $relations = []): LengthAwarePaginator;

    /**
     * Finds a product by its primary key with optional eager-loaded relations. Returns null if not found.
     *
     * @param  int|string  $id  The primary key of the product.
     * @param  array|null  $relations  Optional relations to eager load.
     */
    public function findById(int|string $id, ?array $relations = []): ?Product;

    /**
     * This method creates a new product for a specific business using the provided data.
     *
     * @param StoreProductData $storeProductData The data containing information for creating a new product.
     * @param Business $business The business to which the product belongs.
     * @return Product The newly created product instance.
     */
    public function store(StoreProductData $storeProductData, Business $business): Product;

    /**
     * This method updates an existing product with new data.
     *
     * @param Product $product The product instance to be updated.
     * @return Product The updated product instance.
     */
    public function update(UpdateProductData $updateProductData, Product $product): Product;

    /**
     * This method deletes a product from the database.
     *
     * @param  Product  $product  The product instance to be deleted.
     */
    public function delete(Product $product): void;

    /**
     * Retrieves all products matching the given IDs that belong to the specified business.
     *
     * @param  array  $ids  The list of product IDs to retrieve.
     * @param  Business  $business  The business the products must belong to.
     * @return Collection<int, Product>
     */
    public function findByIdsForBusiness(array $ids, Business $business): Collection;

    /**
     * Soft-deletes all products in the given collection in a single query.
     *
     * @param  Collection<int, Product>  $products  The products to delete.
     */
    public function deleteMany(Collection $products): void;

}
