<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandsSeeder extends Seeder
{
    public function run(): void
    {
        $brands = ['Nike', 'Adidas', 'Apple', 'Samsung', 'Coca-cola', 'Sony', 'Tesla', 'Microsoft', 'Toyota'];

        // Create the 9 fixed brands
        foreach ($brands as $name) {
            Brand::factory()->create(['name' => $name]);
        }

        // // Generate additional random brands to reach 500 total
        // $remaining = 50 - count($brands); // 500 - 9 = 491
        // Brand::factory()->count($remaining)->create();
    }
}
