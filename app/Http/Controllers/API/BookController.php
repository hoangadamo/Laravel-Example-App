<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookCollection;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    protected $bookModel;

    public function __construct(Book $book)
    {
        $this->bookModel = $book;
    }
    public function createBook(CreateBookRequest $request)
    {
        try {
            $book = $this->bookModel->createBook($request);
            $bookResource = new BookResource($book);
            return response()->json(['book' => $bookResource], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Create book failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function getListOfBooks(Request $request)
    {
        try {
            $limit = $request->query('limit', 10);
            $books = $this->bookModel->paginate($limit);
            $bookCollection = new BookCollection($books);
            return response()->json($bookCollection, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Get list of books failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function getBookDetails($id)
    {
        try {
            $book = $this->bookModel->getBookById($id);
            if (!$book) {
                return response()->json(['message' => 'Book not found'], 404);
            }
            $bookResource = new BookResource($book);
            return response()->json(['book' => $bookResource], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Get book detail failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateBook(UpdateBookRequest $request, $id)
    {
        try {
            $book = $this->bookModel->getBookById($id);
            if (!$book) {
                return response()->json(['error' => 'Book not found'], 404);
            }
            $book->updateBook($request, $id);
            $updatedBook = $this->bookModel->getBookById($id);
            $bookResource = new BookResource($updatedBook);
            return response()->json(['book' => $bookResource], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Update book failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteBook($id)
    {
        try {
            $book = $this->bookModel->getBookById($id);
            if (!$book) {
                return response()->json(['error' => 'Book not found'], 404);
            }
            $this->bookModel->deleteBook($id);
            // Detach all books_categories related to the book
            $book->categories()->detach();
            return response()->json(['message' => 'Book deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Delete book failed', 'message' => $e->getMessage()], 500);
        }
    }
}
