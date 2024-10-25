<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('books_categories')->insert([
            [
                'bookId' => 1,
                'categoryId' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
