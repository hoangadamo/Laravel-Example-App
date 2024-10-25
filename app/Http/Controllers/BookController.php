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
        $data = [
            'title' => $request->title,
            'publishedDate' => $request->publishedDate,
            'userId' => auth()->id(), // Get the authenticated user's ID
        ];

        $book = Book::create($data);
        $book->bookCategories()->attach($request->categoryIds);

        return redirect(route('book.index'))->with('success', 'Book created successfully.');
    }

    public function edit(Book $book, $id)
    {
        return response()->json($book->findOrFail($id));
    }

    public function update(UpdateBookRequest $request, Book $book, $id)
    {
        $book = Book::findOrFail($id);

        $data = [
            'title' => $request->title,
            'publishedDate' => $request->publishedDate,
        ];

        $book->update(array_filter($data));

        if ($request->has('categoryIds')) {
            $book->bookCategories()->sync($request->input('categoryIds'));
        }

        return redirect()->route('book.index')->with('success', 'Book updated successfully.');
    }


    public function destroy(Book $book, $id)
    {
        $book->deleteBook($id);
        return redirect(route('book.index'))->with('success', 'book deleted successfully');
    }
}
