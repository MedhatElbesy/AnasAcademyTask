<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_product()
    {
        $category = category::create(['name' => 'Electronicss']);

        $response = $this->post('/api/products', [
            'name'        => 'Laptop',
            'price'       => 1500,
            'quantity'    => 10,
            'category_id' => $category->id,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', ['name' => 'Laptop']);
    }
}
