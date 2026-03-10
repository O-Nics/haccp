<?php

declare(strict_types=1);

namespace App\Modules\Product\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Exceptions\NotFoundException;
use App\Core\Resources\BaseAnonymousCollection;
use App\Modules\Product\Requests\DeleteProductRequest;
use App\Modules\Product\Requests\IndexProductRequest;
use App\Modules\Product\Requests\StoreProductRequest;
use App\Modules\Product\Requests\UpdateProductRequest;
use App\Modules\Product\Resources\ProductResource;
use Domain\Business\Models\Business;
use Domain\Product\Actions\DeleteProductAction;
use Domain\Product\Actions\StoreProductAction;
use Domain\Product\Actions\UpdateProductAction;
use Domain\Product\Interfaces\ProductRepositoryInterface;
use Domain\Product\Models\Product;
use Illuminate\Http\Response;

final class ProductController extends BaseController
{
    /**
     * Returns a paginated list of products for the given business.
     */
    public function index(IndexProductRequest $indexProductRequest, Business $business, ProductRepositoryInterface $productRepository): BaseAnonymousCollection
    {
        $products = $productRepository->getFilteredPaginated($indexProductRequest->toDto(), $business);

        return ProductResource::collection($products);
    }

    /**
     * Creates a new product for the given business. Returns 201 on success.
     */
    public function store(StoreProductRequest $storeProductRequest, Business $business, StoreProductAction $storeProductAction): ProductResource
    {
        $product = $storeProductAction->handle($storeProductRequest->toDto(), $business);

        return new ProductResource($product)->status(201);
    }

    /**
     * Partially updates a product (PATCH semantics). Returns 200 on success.
     *
     * @throws NotFoundException 404 if the product belongs to a different business
     */
    public function update(UpdateProductRequest $updateProductRequest, Business $business, Product $product, UpdateProductAction $updateProductAction): ProductResource
    {
        $product = $updateProductAction->handle($updateProductRequest->toDto(), $business, $product);

        return new ProductResource($product);
    }

    /**
     * Bulk-deletes products by ID for the given business. Returns 204 on success.
     *
     * @throws NotFoundException 404 if any product belongs to a different business
     */
    public function deleteMany(DeleteProductRequest $deleteProductRequest, Business $business, DeleteProductAction $deleteProductAction): Response
    {
        $deleteProductAction->handle($deleteProductRequest->toDto(), $business);

        return response()->noContent();
    }
}
