<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AddProduct;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class AddProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::pluck('id')->toArray();

        if (empty($products)) {
            $this->command->warn('⚠️ No products found — seed the Product table first.');
            return;
        }

        AddProduct::factory()
            ->count(5)
            ->create()
            ->each(function ($addProduct) use ($products) {
                $randomProducts = array_rand(array_flip($products), rand(1, min(3, count($products))));

                foreach ((array)$randomProducts as $pid) {
                    DB::table('add_products_to_list')->insert([
                        'add_product_id' => $addProduct->id,
                        'product_id'     => $pid,
                        'quantity'       => rand(1, 10),
                    ]);
                }
            });
    }
}
