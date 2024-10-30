<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

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


    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'books_categories', 'bookId', 'categoryId');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'books_orders', 'bookId', 'orderId')->withPivot('quantity');
    }

    public function getBooks()
    {
        return $this->get();
    }


    public function getBookById($id)
    {
        return $this->where('id', $id)->first();
    }

    public function deleteBook($id)
    {
        $this->where('id', $id)->delete();
    }

    public function updateBook($request, $id)
    {
        $data = [
            'title' => $request->title,
            'publishedDate' => $request->publishedDate
        ];

        $this->where('id', $id)->update(array_filter($data));

        if ($request->has('categoryIds')) {
            $this->categories()->sync($request->input('categoryIds'));
        }
    }

    public function createBook($request)
    {
        $user = Auth::user();
        $data = [
            'title' => $request->title,
            'publishedDate' => $request->publishedDate,
            'userId' => $user->id,
        ];
        $book = $this->create($data);
        $book->categories()->attach($request->categoryIds);
        return $book;
    }
}
