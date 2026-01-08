<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ReturnItem;
use Illuminate\Database\Seeder;

class ReturnItemSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::pluck('id')->toArray();

        if (!empty($products)) {
            ReturnItem::factory()
                ->count(100000)
                ->create()
                ->each(function ($returnItem) use ($products) {
                    $randomProducts = array_rand(array_flip($products), rand(1, min(3, count($products))));
                    foreach ((array)$randomProducts as $pid) {
                        $returnItem->products()->attach($pid,[
                    'quantity'   => rand(1, 10000),
                    'unit_price' => rand(
                        floor($product->purchase_price * 0.8),
                        ceil($product->sale_price * 1.2)
                    ),
                ]);
                    }
                });
        }
    }
}
