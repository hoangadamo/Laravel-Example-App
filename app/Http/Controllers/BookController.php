<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Category;
use App\Repositories\BookRepository;
use App\Services\BookService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    protected $bookSerivce;
    protected $bookRepository;
    protected $categoryModel;

    public function __construct(BookService $bookSerivce, BookRepository $bookRepository, Category $category)
    {
        $this->bookSerivce = $bookSerivce;
        $this->bookRepository = $bookRepository;
        $this->categoryModel = $category;
    }

    public function index()
    {
        $books = $this->bookRepository->get();
        $categories = $this->categoryModel->get();
        return view('books.index', compact('books', 'categories'));
    }


    public function store(CreateBookRequest $request)
    {
        $book = $this->bookSerivce->createBook($request);
        return redirect(route('book.index'))->with('success', 'Book created successfully.');
    }

    public function edit($id)
    {
        $book = $this->bookRepository->getById($id);
        return response()->json($book);
    }

    public function update($id, UpdateBookRequest $request)
    {
        $book = $this->bookSerivce->getBookById($id);
        if (!$book) {
            return redirect()->back()->with('error', 'Book not found')->withInput();
        }
        $this->bookSerivce->updateBook($id, $request);

        return redirect()->route('book.index')->with('success', 'Book updated successfully.');
    }

    public function destroy($id)
    {
        $this->bookSerivce->deleteBook($id);
        return redirect(route('book.index'))->with('success', 'book deleted successfully');
    }
}
