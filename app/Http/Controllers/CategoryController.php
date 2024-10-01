<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\category;
use Exception;
use Illuminate\Http\Request;
use Throwable;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(10);
        if($categories){
            return ApiResponse::sendResponse(200, 'Categories fetched successfully', CategoryResource::collection($categories));
        }
        return ApiResponse::sendResponse(404,'No Categories');
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $category = category::create($request->validated());
            return ApiResponse::sendResponse(201, 'Category created successfully', new CategoryResource($category));
        } catch (Throwable $th) {
            return ApiResponse::sendResponse(404,'Fail to Create Category');
        }
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->validated());

        return ApiResponse::sendResponse(200, 'Category updated successfully', new CategoryResource($category));
    }
}
