<?php

namespace App\Services;

use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Repositories\CategoryRepository;

class CategoryService
{

    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function createCategory($request)
    {
        try {
            $data = [
                'name' => $request->name,
                'description' => $request->description,
            ];
            $category = $this->categoryRepository->create($data);
            $categoryResource = new CategoryResource($category);
            return response()->json($categoryResource, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Create category failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function getCategories($limit)
    {
        try {
            $categories = $this->categoryRepository->paginate($limit);
            $categoryCollection = new CategoryCollection($categories);
            return response()->json($categoryCollection, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Get list of categories failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function getCategoryById($id)
    {
        try {
            $category = $this->categoryRepository->getById($id);
            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }
            $categoryResource = new CategoryResource($category);
            return response()->json($categoryResource, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Get category detail failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateCategory($id, $request)
    {
        try {
            $category = $this->categoryRepository->getById($id);
            if (!$category) {
                return response()->json(['error' => 'Category not found'], 404);
            }
            $data = [
                'name' => $request->name,
                'description' => $request->description
            ];

            $this->categoryRepository->update($id, $data);
            $updatedCategory = $this->categoryRepository->getById($id);
            $categoryResource = new CategoryResource($updatedCategory);
            return response()->json($categoryResource, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Update category failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteCategory($id)
    {
        try {
            $category = $this->categoryRepository->getById($id);
            if (!$category) {
                return response()->json(['error' => 'Category not found'], 404);
            }
            $this->categoryRepository->delete($id);
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
            $category = $this->categoryRepository->getById($id);
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
