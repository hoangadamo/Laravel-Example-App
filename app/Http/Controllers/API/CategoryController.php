<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryModel;

    public function __construct(Category $category)
    {
        $this->categoryModel = $category;
    }

    public function getListOfCategories(Request $request)
    {
        try {
            $limit = $request->query('limit', 10);
            $categories = $this->categoryModel->paginate($limit);
            $categoryCollection = new CategoryCollection($categories);
            return response()->json($categoryCollection, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Get list of categories failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function getCategoryDetails($id)
    {
        try {
            $category = $this->categoryModel->getCategoryById($id);
            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }
            $categoryResource = new CategoryResource($category);
            return response()->json($categoryResource, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Get category detail failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function createCategory(CreateCategoryRequest $request)
    {
        try {
            $category = $this->categoryModel->createCategory($request);
            $categoryResource = new CategoryResource($category);
            return response()->json($categoryResource, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Create category failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateCategory(UpdateCategoryRequest $request, $id)
    {
        try {
            $category = $this->categoryModel->getCategoryById($id);
            if (!$category) {
                return response()->json(['error' => 'Category not found'], 404);
            }
            $category->updateCategory($request, $id);
            $updatedCategory = $this->categoryModel->getCategoryById($id);
            $categoryResource = new CategoryResource($updatedCategory);
            return response()->json($categoryResource, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Update category failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteCategory($id)
    {
        try {
            $category = $this->categoryModel->getCategoryById($id);
            if (!$category) {
                return response()->json(['error' => 'Category not found'], 404);
            }
            $this->categoryModel->deleteCategory($id);
            // Detach all books_categories related to the category
            $category->books()->detach();
            return response()->json(['message' => 'Category deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Delete category failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function getAllCategoryBooks($id)
    {
        try {
            $category = $this->categoryModel->getCategoryById($id);
            if (!$category) {
                return response()->json(['error' => 'Category not found'], 404);
            }

            $books = $category->books;
            return response()->json($books, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Get all books of category failed', 'message' => $e->getMessage()], 500);
        }
    }
}
