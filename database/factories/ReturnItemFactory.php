<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ReturnItem;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReturnItem>
 */
class ReturnItemFactory extends Factory
{
    protected $model = ReturnItem::class;

    public function definition(): array
    {
        $statuses = ['pending', 'approved', 'rejected'];
        $types = ['refunded', 'exchanged'];
        $reasons = [
            'Item was defective upon delivery.',
            'Received wrong item from sale.',
            'Item used and cannot be returned.',
            'Customer changed their mind.',
            'Damaged during shipment.',
        ];

        return [
            'return_type' => $this->faker->randomElement($types),
            'status'      => $this->faker->randomElement($statuses),
            'reason'      => $this->faker->randomElement($reasons),
        ];
    }

    
 public function configure()
    {
        return $this->afterCreating(function (ReturnItem $return): void {
            $products = Product::all();
            if ($products->isEmpty()) return;

            $randomProducts = $products->random(rand(1, min(5, $products->count())));

            foreach ($randomProducts as $product) {
                $maxQty = max(1, min(5, $product->inventory_balance));
                $return->products()->attach($product->id, [
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
