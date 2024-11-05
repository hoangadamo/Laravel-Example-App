<?php

namespace App\Repositories;

use App\Models\Book;

class BookRepository
{
    protected $book;
    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    public function create($data)
    {
        return $this->book->create($data);
    }

    public function get()
    {
        return $this->book->get();
    }

    public function getById($id)
    {
        return $this->book->where('id', $id)->first();
    }

    public function update($id, $data)
    {
        return $this->book->where('id', $id)->update($data);
    }

    public function delete($id)
    {
        $this->book->where('id', $id)->delete();
    }

    public function paginate($limit)
    {
        return $this->book->paginate($limit);
    }
}
