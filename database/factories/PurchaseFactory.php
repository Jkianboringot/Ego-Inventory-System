<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition(): array
    {
        $supplierIds = Supplier::pluck('id')->toArray();
        $products = Product::all();

        return [
            'supplier_id' => $this->faker->randomElement($supplierIds),
            'date_settled' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'is_paid' => $this->faker->randomElement(['Paid', 'Unpaid']),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Purchase $purchase) {
            $products = Product::all();
            $randomProducts = $products->random(rand(1, min(5, $products->count())));

            foreach ($randomProducts as $product) {
                $purchase->products()->attach($product->id, [
                    'quantity' => rand(20, 1000),
                    'unit_price' => $product->sale_price,
                ]);
            }
        });
    }
}
