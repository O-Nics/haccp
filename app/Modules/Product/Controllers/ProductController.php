<?php

namespace App\Modules\Product\Controllers;

use App\Core\Controllers\BaseController;
use App\Core\Exceptions\NotFoundException;
use App\Core\Resources\BaseAnonymousCollection;
use App\Modules\Product\Requests\DeleteProductRequest;
use App\Modules\Product\Requests\IndexProductRequest;
use App\Modules\Product\Requests\StoreProductRequest;
use App\Modules\Product\Requests\UpdateProductRequest;
use App\Modules\Product\Resources\ProductResource;
use Domain\Business\Actions\DeleteMemberAction;
use Domain\Business\Models\Business;
use Domain\Product\Action\DeleteProductAction;
use Domain\Product\Action\StoreProductAction;
use Domain\Product\Action\UpdateProductAction;
use Domain\Product\Ensures\ProductEnsure;
use Domain\Product\Interfaces\ProductRepositoryInterface;
use Domain\Product\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     * This method retrieves a paginated list of products for a specific business, applying any filters and sorting options provided in the request. It returns a JSON response containing a collection of products.
     * @param IndexProductRequest $indexProductRequest The request object containing the filters and sorting options for retrieving the products.
     * @param Business $business The business for which to retrieve the products.
     * @throws ValidationException If the request validation fails.
     * @return BaseAnonymousCollection A JSON response containing a collection of products.
     *
     */
    public function index(
        IndexProductRequest $indexProductRequest,
        Business $business,
        ProductRepositoryInterface $productRepository
    ): BaseAnonymousCollection
    {
        $products = $productRepository->getFilteredPaginated($indexProductRequest->toDto(), $business);

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     * This method creates a new product for a specific business using the data provided in the request. It validates the request data, creates the product, and returns a JSON response containing the newly created product.
     * @param StoreProductRequest $storeProductRequest The request object containing the data for creating the product.
     * @param Business $business The business for which to create the product.
     * @throws ValidationException If the request validation fails.
     * @return ProductResource A JSON response containing the newly created product.
     */
    public function store(
        StoreProductRequest $storeProductRequest,
        Business $business,
        StoreProductAction $storeProductAction
    ): ProductResource
    {
        $product = $storeProductAction->handle($storeProductRequest->toDto(), $business);

        return new ProductResource($product)->status(204);

    }

    /**
     * Update the specified resource in storage.
     * @throws NotFoundException
     */
    public function update(
        UpdateProductRequest $updateProductRequest,
        Business $business,
        Product $product,
        UpdateProductAction $updateProductAction
    ): ProductResource
    {
        $product = $updateProductAction->handle($updateProductRequest->toDto(), $business, $product);
        return new ProductResource($product);
    }


    /**
     */
    public function delete(DeleteProductRequest $deleteProductRequest, Business $business, DeleteProductAction $deleteProductAction): Response
    {
        $deleteProductAction->handle($deleteProductRequest->toDto(), $business);
        return response()->noContent();
    }
}
