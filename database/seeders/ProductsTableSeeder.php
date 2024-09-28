<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'Product_1',
                'price' => 19.99,
                'quantity' => 50
            ],
            [
                'name' => 'Product_2',
                'price' => 19.99,
                'quantity' => 50
            ],
            [
                'name' => 'Product_3',
                'price' => 19.99,
                'quantity' => 50
            ],
            [
                'name' => 'Product_4',
                'price' => 19.99,
                'quantity' => 50
            ],
        ]);
    }
}
