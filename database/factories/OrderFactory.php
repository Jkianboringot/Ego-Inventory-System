<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $customer = Customer::inRandomOrder()->first();

        // Use timestamp + random number to ensure unique orders_ref
        $uniqueRef = 'S-' . Str::upper(Str::random(8));

        return [
            'orders_ref'   => $uniqueRef,
            'customer_id'  => $customer->id ?? null,
            'created_at'   => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at'   => now(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            $products = Product::all();
            if ($products->isEmpty()) return;

            $randomProducts = $products->random(rand(1, min(5, $products->count())));

            foreach ($randomProducts as $product) {
                $maxQty = max(1, min(5, $product->inventory_balance));
                $order->products()->attach($product->id, [
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
