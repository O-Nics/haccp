<?php

namespace App\Modules\Product\Controllers;

use App\Core\Controllers\BaseController;
use App\Modules\Product\Requests\StoreProductRequest;
use App\Modules\Product\Requests\UpdateProductRequest;
use Domain\Business\Models\Business;
use Domain\Product\Models\Product;

class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request, Business $business)
    {

    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Business $business, Product $product)
    {
        //
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
