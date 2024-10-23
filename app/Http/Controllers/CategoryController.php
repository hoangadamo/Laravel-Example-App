<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function list(Request $request)
    {
        // $limit = $request->query('limit',10);
        // $categories =  Category::paginate($limit);;
        $categories =  Category::all();
        return response()->json($categories);
    }

    // public function details(Category $category)
    // {
    //     return response()->json($category);
    // }

    public function details($id)
    {
        try {
            $category = Category::find($id);
            if (!$category) {
                return response()->json(['error' => 'Category not found'], 404);
            }
            return response()->json($category);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    public function create(CreateCategoryRequest $request)
    {
        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        $category = Category::create($data);

        return response()->json($category, 201);
    }

    public function delete($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
