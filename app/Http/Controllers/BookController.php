<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    protected $book;

    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    public function index()
    {
        $books = $this->book->getBooks();
        $categories = Category::all();
        return view('books.index', compact('books', 'categories'));
    }


    public function store(CreateBookRequest $request)
    {
        $book = $this->book->createBook($request);
        return redirect(route('book.index'))->with('success', 'Book created successfully.');
    }

    public function edit(Book $book, $id)
    {
        return response()->json($book->findOrFail($id));
    }

    public function update(UpdateBookRequest $request, $id)
    {
        $book = $this->book->getBookById($id);
        if (!$book) {
            return redirect()->back()->with('error', 'Book not found')->withInput();
        }
        $book->updateBook($request, $id);

        return redirect()->route('book.index')->with('success', 'Book updated successfully.');
    }


    public function destroy($id)
    {
        $this->book->deleteBook($id);
        return redirect(route('book.index'))->with('success', 'book deleted successfully');
    }
}
