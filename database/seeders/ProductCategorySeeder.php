<?php

namespace Database\Seeders;

use App\Models\category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $products = Product::all();
        $categoryIds = category::pluck('id')->toArray();

        foreach ($products as $product) {
            $product->update([
                'category_id' => $categoryIds[array_rand($categoryIds)]
            ]);
        }
    }
}
