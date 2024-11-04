<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function createCategory(CreateCategoryRequest $request)
    {
        return $this->categoryService->createCategory($request);
    }

    public function getListOfCategories(Request $request)
    {
        $limit = $request->query('limit', 10);
        return $this->categoryService->getCategories($limit);
    }

    public function getCategoryDetails($id)
    {
        return $this->categoryService->getCategoryById($id);
    }

    public function updateCategory($id, UpdateCategoryRequest $request)
    {
        return $this->categoryService->updateCategory($id, $request);
    }

    public function deleteCategory($id)
    {
        return $this->categoryService->deleteCategory($id);
    }

    public function getAllCategoryBooks($id)
    {
        return $this->categoryService->getAllCategoryBooks($id);
    }
}
