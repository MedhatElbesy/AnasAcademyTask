<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table(table: 'categories')->insert([
            [
                'name' => 'Category_1',
            ],
            [
                'name' => 'Category_2',
            ],
            [
                'name' => 'Category_3',
            ],
            [
                'name' => 'Category_4',
            ],
        ]);
    }
}
