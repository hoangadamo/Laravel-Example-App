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
}
