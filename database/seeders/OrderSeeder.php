<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('orders')->insert([
            [
                'title' => 'Romeo and Juliet',
                'publishedDate' => '2023-01-01',
                'isApproved' => true,
                'userId' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
