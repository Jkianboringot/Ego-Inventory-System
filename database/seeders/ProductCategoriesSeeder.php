<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;

class ProductCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        // Create 10 random categories
        ProductCategory::factory()->count(10)->create();
    }
}
