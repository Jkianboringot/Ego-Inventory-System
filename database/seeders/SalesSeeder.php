<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesSeeder extends Seeder
{
    public function run(): void
    {

        $products = Product::all();

        Sale::factory()
            ->count(100000)
            ->create()
            ->each(function ($sale) use ($products) {
                $randomProducts = $products->random(rand(1, 5));
                foreach ($randomProducts as $product) {
                    $maxQty = max(1, min(5, $product->inventory_balance));
                    $sale->products()->attach($product->id, [
                        'quantity'   => rand(1, $maxQty),
                        'unit_price' => rand(
                            floor($product->purchase_price * 0.8),
                            ceil($product->sale_price * 1.2)
                        ),
                    ]);
                }
            });
    }
}
