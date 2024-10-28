<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
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

        $category->where('id', $id)->update(array_filter($data));

        return redirect()->route('category.index')->with('success', 'category updated successfully.');
    }

    public function destroy(Category $category, $id)
    {
        $category->where('id', $id)->delete();
        return redirect()->route('category.index')->with('success', 'category deleted successfully.');
    }
}
