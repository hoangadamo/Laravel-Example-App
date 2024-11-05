<?php

namespace App\Services;

use App\Http\Resources\BookCollection;
use App\Http\Resources\BookResource;
use App\Repositories\BookRepository;
use Illuminate\Support\Facades\Auth;

class BookService
{
    protected $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function createBook($request)
    {
        try {
            $user = Auth::user();
            $data = [
                'title' => $request->title,
                'publishedDate' => $request->publishedDate,
                'userId' => $user->id,
            ];
            $book = $this->bookRepository->create($data);
            $book->categories()->attach($request->categoryIds);
            $bookResource = new BookResource($book);
            return response()->json(['message' => 'Create book successfully', 'book' => $bookResource], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Create book failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function getBooks($limit)
    {
        try {
            $books = $this->bookRepository->paginate($limit);
            $bookCollection = new BookCollection($books);
            return response()->json($bookCollection, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Get list of books failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function getBookById($id)
    {
        try {
            $book = $this->bookRepository->getById($id);
            if (!$book) {
                return response()->json(['message' => 'Category not found'], 404);
            }
            $bookResource = new BookResource($book);
            return response()->json($bookResource, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Get book detail failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateBook($id, $request)
    {
        try {
            $book = $this->bookRepository->getById($id);
            if (!$book) {
                return response()->json(['error' => 'Book not found'], 404);
            }
            $data = [
                'title' => $request->title,
                'publishedDate' => $request->publishedDate
            ];

            $this->bookRepository->update($id, array_filter($data));

            if ($request->has('categoryIds')) {
                $book->categories()->sync($request->input('categoryIds'));
            }
            $updatedBook = $this->bookRepository->getById($id);
            $bookResource = new BookResource($updatedBook);
            return response()->json(['book' => $bookResource], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Update book failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteBook($id)
    {
        try {
            $book = $this->bookRepository->getById($id);
            if (!$book) {
                return response()->json(['error' => 'Book not found'], 404);
            }
            $this->bookRepository->delete($id);
            // Detach all books_categories related to the book
            $book->categories()->detach();
            return response()->json(['message' => 'Book deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Delete book failed', 'message' => $e->getMessage()], 500);
        }
    }
}
