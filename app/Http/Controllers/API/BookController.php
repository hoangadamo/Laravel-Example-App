<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookCollection;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    protected $bookSerivce;

    public function __construct(BookService $bookSerivce)
    {
        $this->bookSerivce = $bookSerivce;
    }
    public function createBook(CreateBookRequest $request)
    {
        return $this->bookSerivce->createBook($request);
    }

    public function getListOfBooks(Request $request)
    {
        $limit = $request->query('limit', 10);
        return $this->bookSerivce->getBooks($limit);
    }

    public function getBookDetails($id)
    {
        return $this->bookSerivce->getBookById($id);
    }

    public function updateBook(UpdateBookRequest $request, $id)
    {
        return $this->bookSerivce->updateBook($id, $request);
    }

    public function deleteBook($id)
    {
        return $this->bookSerivce->deleteBook($id);
    }
}
