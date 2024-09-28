<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all()->paginate(10);
        return ApiResponse::sendResponse(200, 'Categories fetched successfully', CategoryResource::collection($categories));
    }
    public function store(StoreCategoryRequest $request)
    {
        $category = category::create($request->validated());
        return ApiResponse::sendResponse(201, 'Category created successfully', new CategoryResource($category));
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->validated());

        return ApiResponse::sendResponse(200, 'Category updated successfully', new CategoryResource($category));
    }

}
