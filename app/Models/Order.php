<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'userId',
        'orderDate',
        'totalAmount',
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'books_orders', 'orderId', 'bookId')->withPivot('quantity');;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function createOrder($request)
    {
        $totalAmount = 0;
        foreach ($request->books as $book) {
            $currentBook = Book::find($book['bookId']);
            $totalAmount += $book['quantity'] * $currentBook->price;
        }

        $data = [
            'userId' => $request->userId,
            'orderDate' => now(),
            'totalAmount' => $totalAmount
        ];

        $order = $this->create($data);

        foreach ($request->books as $book) {
            $order->books()->attach($book['bookId'], [
                'quantity' => $book['quantity'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return $order;
    }

    public function storeOrderInCache($request)
    {
        $totalAmount = 0;
        $books = [];

        foreach ($request->books as $book) {
            $currentBook = Book::find($book['bookId']);
            $totalAmount += $book['quantity'] * $currentBook->price;
            $books[] = [
                'bookId' => $book['bookId'],
                'quantity' => $book['quantity'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $token = Str::uuid();
        $orderData = [
            'userId' => 7,
            'orderDate' => now(),
            'totalAmount' => $totalAmount,
            'books' => $books,
            'token' => $token,
        ];

        Cache::put('order_' . $token, json_encode($orderData), now()->addMinutes(10));

        return $orderData;
    }

    public function getOrders()
    {
        return $this->get();
    }


    public function getOrderById($id)
    {
        return $this->where('id', $id)->first();
    }

    public static function filterOrders($filters)
    {
        $query = self::with(['books', 'books.categories', 'user'])
            ->whereHas('books', function ($query) {
                $query->where('isApproved', true);
            })
            ->when(isset($filters['totalAmountMin']) && isset($filters['totalAmountMax']), function ($query) use ($filters) {
                $query->whereBetween('totalAmount', [$filters['totalAmountMin'], $filters['totalAmountMax']]);
            })
            ->when(isset($filters['orderDate']), function ($query) use ($filters) {
                $query->whereDate('orderDate', $filters['orderDate']);
            })
            ->when(isset($filters['categories']), function ($query) use ($filters) {
                $query->whereHas('books.categories', function ($query) use ($filters) {
                    $query->whereIn('categories.id', $filters['categories']);
                });
            })
            ->when(isset($filters['publishedDate']), function ($query) use ($filters) {
                $query->whereHas('books', function ($query) use ($filters) {
                    $query->whereDate('publishedDate', $filters['publishedDate']);
                });
            })
            ->when(isset($filters['quantityMin']) && isset($filters['quantityMax']), function ($query) use ($filters) {
                $query->whereIn('orders.id', function ($subquery) use ($filters) {
                    $subquery->select('books_orders.orderId')
                        ->from('books_orders')
                        ->groupBy('books_orders.orderId')
                        ->havingRaw('SUM(books_orders.quantity) BETWEEN ? AND ?', [$filters['quantityMin'], $filters['quantityMax']]);
                });
            });
        // dd($query->get());
        return $query->get();
    }
}
