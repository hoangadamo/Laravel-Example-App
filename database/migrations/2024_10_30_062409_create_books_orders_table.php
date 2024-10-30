<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bookId');
            $table->foreign('bookId')->references('id')->on('books')->onDelete('cascade');
            $table->unsignedBigInteger('orderId');
            $table->foreign('orderId')->references('id')->on('orders')->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books_orders');
    }
};
