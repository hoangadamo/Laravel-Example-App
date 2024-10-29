<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'books_categories', 'categoryId', 'bookId');
    }

    public function getCategories()
    {
        return $this->get();
    }

    public function getCategoryById($id)
    {
        return $this->where('id', $id)->first();
    }

    public function createCategory($request)
    {
        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        $category = $this->create($data);
        return $category;
    }

    public function updateCategory($request, $id)
    {
        $data = [
            'name' => $request->name,
            'description' => $request->description
        ];

        $this->where('id', $id)->update(array_filter($data));
    }

    public function deleteCategory($id)
    {
        $this->where('id', $id)->delete();
    }
}
