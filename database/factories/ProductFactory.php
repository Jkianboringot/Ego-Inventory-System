<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Brand;
use App\Models\ProductCategory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $brandIds = Brand::pluck('id')->toArray();
        $categoryIds = ProductCategory::pluck('id')->toArray();

        return [
            'brand_id' => $this->faker->randomElement($brandIds),
            'product_category_id' => $this->faker->randomElement($categoryIds),
            // Use lexify/bothify for thousands of unique names
            'name' => $this->faker->unique()->bothify('PROD-????-####'),
            'supplier_id' => $this->faker->numberBetween(1, 5),
            'description' => $this->faker->sentence(8),
            'inventory_threshold' => $this->faker->numberBetween(5, 20),
            'unit_id' => 1, // assuming 'Piece'
            'barcode' => $this->faker->unique()->bothify('???###'),
            'purchase_price' => $this->faker->numberBetween(1000, 200000),
            'sale_price' => $this->faker->numberBetween(1500, 250000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
