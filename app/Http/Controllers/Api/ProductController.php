<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function getProductsAbovePrice($price)
    {
        $products = Product::where('price', '>', $price)->get();
        return ApiResponse::sendResponse(200, 'Product fetched successfully', new ProductResource($products));

    }
    public function index()
    {
        $products = Product::all()->paginate(10);
        return ApiResponse::sendResponse(200, 'Products fetched successfully', ProductResource::collection($products));
    }
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());
        return ApiResponse::sendResponse(201, 'Product created successfully', new ProductResource($product));
    }

    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $this->authorize('update', $product);
            $product->update($request->validated());
            return ApiResponse::sendResponse(200, 'Product updated successfully', new ProductResource($product));
        } catch (Exception $e) {
            return ApiResponse::sendResponse(500, 'An error occurred while updating the product: ' . $e->getMessage(), null);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $this->authorize('delete', $product);
            $product->delete();
            return ApiResponse::sendResponse(200, 'Product deleted successfully', null);
        } catch (Exception $e) {
            return ApiResponse::sendResponse(500, 'An error occurred while deleting the product: ' . $e->getMessage(), null);
        }
    }
}
