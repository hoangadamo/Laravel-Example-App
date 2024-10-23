<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    // api
    public function list(Request $request)
    {
        $categories =  Category::all();
        return response()->json($categories);
    }

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

    public function updateApi(UpdateCategoryRequest $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }
        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];
        $category->update(array_filter($data));
        return response()->json($category, 200);
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

    // views
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    public function store(CreateCategoryRequest $request)
    {
        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        Category::create($data);
        return redirect(route('category.index'));
    }

    public function edit(Category $category, $id)
    {
        return response()->json($category->findOrFail($id));
    }

    public function update(UpdateCategoryRequest $request, Category $category, $id)
    {

        $data = [
            'name' => $request->name,
            'description' => $request->description
        ];

        // dd($data);
        $category->where('id', $id)->update(array_filter($data));

        return redirect()->route('category.index')->with('success', 'category updated successfully.');
    }

    public function destroy(Category $category, $id)
    {
        $category->where('id', $id)->delete();
        return redirect(route('category.index'))->with('success', 'category deleted successfully');
    }
}
