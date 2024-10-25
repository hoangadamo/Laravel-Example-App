<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'publishedDate',
        'isApproved',
        'userId',
    ];

    protected $casts = [
        'publishedDate' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }


    public function bookCategories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'books_categories', 'bookId', 'categoryId');
    }

    public function getBooks()
    {
        return $this->with(['user', 'bookCategories'])->get();
    }


    public function getBookById($id)
    {
        return $this->with('user', 'bookCategories')->where('id', $id)->first();
    }

    public function deleteBook($id)
    {
        $this->where('id', $id)->delete();
    }
}
