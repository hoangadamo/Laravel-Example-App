<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'userName' => $this->user->name,
            'orderDate' => $this->orderDate,
            'totalAmount' => $this->totalAmount,
            'books' => $this->books->map(function ($book) {
                return [
                    'title' => $book->title,
                    'quantity' => $book->pivot->quantity,
                ];
            }),
        ];
    }
}
