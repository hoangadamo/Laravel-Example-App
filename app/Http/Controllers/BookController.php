<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    protected $bookModel;

    public function __construct(Book $book)
    {
        $this->bookModel = $book;
    }

    public function index()
    {
        $books = $this->bookModel->getBooks();
        $categories = Category::all();
        return view('books.index', compact('books', 'categories'));
    }


    public function store(CreateBookRequest $request)
    {
        $book = $this->bookModel->createBook($request);
        return redirect(route('book.index'))->with('success', 'Book created successfully.');
    }

    public function edit(Book $book, $id)
    {
        return response()->json($book->findOrFail($id));
    }

    public function update(UpdateBookRequest $request, $id)
    {
        $book = $this->bookModel->getBookById($id);
        if (!$book) {
            return redirect()->back()->with('error', 'Book not found')->withInput();
        }
        $book->updateBook($request, $id);

        return redirect()->route('book.index')->with('success', 'Book updated successfully.');
    }


    public function destroy($id)
    {
        $this->bookModel->deleteBook($id);
        return redirect(route('book.index'))->with('success', 'book deleted successfully');
    }
}
