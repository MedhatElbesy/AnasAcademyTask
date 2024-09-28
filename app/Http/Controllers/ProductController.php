<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\category;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::with('category')->get();
        $categories = category::all();
        return view('products.index', compact('products', 'categories'));
    }

    public function store(StoreProductRequest $request)
{
    $product = Product::create($request->validated());

    if ($request->ajax()) {
        return ApiResponse::sendResponse(200,'Product added successfully.',$product->load('category'));
    }

    return redirect()->route('products.index')->with('success', 'Product added successfully.');
}

}
