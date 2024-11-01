<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    protected $categoryModel;

    public function __construct(Category $category)
    {
        $this->categoryModel = $category;
    }

    public function index()
    {
        $categories = $this->categoryModel->getCategories();
        return view('categories.index', compact('categories'));
    }

    public function store(CreateCategoryRequest $request)
    {
        $categories = $this->categoryModel->createCategory($request);
        return redirect(route('category.index'));
    }

    public function edit(Category $category, $id)
    {
        return response()->json($category->findOrFail($id));
    }

    public function update(UpdateCategoryRequest $request, Category $category, $id)
    {
        $this->categoryModel->updateCategory($request, $id);
        return redirect()->route('category.index')->with('success', 'category updated successfully.');
    }

    public function destroy($id)
    {
        $this->categoryModel->deleteCategory($id);
        return redirect()->route('category.index')->with('success', 'category deleted successfully.');
    }
}
