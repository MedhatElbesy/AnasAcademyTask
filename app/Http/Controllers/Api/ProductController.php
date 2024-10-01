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
use Throwable;

class ProductController extends Controller
{
    public function getProductsAbovePrice($price)
    {
        $products = Product::where('price', '>', $price)->get();
        if($products){
            return ApiResponse::sendResponse(200, 'Products fetched successfully', ProductResource::collection($products));
        }
        return ApiResponse::sendResponse(404,'Can`t Find');
    }

    public function index()
    {
        $products = Product::paginate(10);
        if($products){
            return ApiResponse::sendResponse(200, 'Products fetched successfully', ProductResource::collection($products));
        }
            return ApiResponse::sendResponse(404,'No Products');
    }
    public function store(StoreProductRequest $request)
    {
        try {
            $userId = auth()->id();
            $data = array_merge($request->validated(), ['user_id' => $userId]);
            $product = Product::create($data);

            return ApiResponse::sendResponse(201, 'Product created successfully', new ProductResource($product));
        } catch (Throwable $th) {
            return ApiResponse::sendResponse(404, 'Fail to Create Product');
        }
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
            return ApiResponse::sendResponse(200, 'Product deleted successfully');
        } catch (Exception $e) {
            return ApiResponse::sendResponse(500, 'An error occurred while deleting the product: ' . $e->getMessage(), null);
        }
    }
}
