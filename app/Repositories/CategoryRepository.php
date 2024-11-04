<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    protected $category;
    public function __construct(Category $category)
    {
        $this->category = $category;
    }
    public function create($data)
    {
        return $this->category->create($data);
    }

    public function get()
    {
        return $this->category->get();
    }

    public function getById($id)
    {
        return $this->category->where('id', $id)->first();
    }

    public function update($id, $data)
    {
        return $this->category->where('id', $id)->update(array_filter($data));
    }

    public function delete($id)
    {
        $this->category->where('id', $id)->delete();
    }

    public function paginate($limit)
    {
        $this->category->paginate($limit);
    }
}
