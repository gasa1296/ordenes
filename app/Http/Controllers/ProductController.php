<?php

namespace App\Http\Controllers;

use App\Http\Resources\Products\ProductResource;
use App\Models\Product;
use App\Repositories\Interfaces\IProductRepository;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private IProductRepository $productRepository,
    )
    {

    }
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $models = $this->productRepository->all();
        return ProductResource::collection($models)->response();
    }
    /**
     * Display the specified resource.
     */
    public function show(Product $product): JsonResponse
    {
        return (new ProductResource($product))->response();
    }
}
