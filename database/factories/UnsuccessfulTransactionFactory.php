<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UnsuccessfulTransaction;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UnsuccessfulTransaction>
 */
class UnsuccessfulTransactionFactory extends Factory
{
    protected $model = UnsuccessfulTransaction::class;

    public function definition(): array
    {
        $statuses = ['pending', 'pending', 'pending', 'rejected', 'approved'];

        return [
            'status' => $statuses[array_rand($statuses)],
        ];
    }

    /**
     * Attach random products to the transaction.
     */
    public function configure()
    {
        return $this->afterCreating(function (UnsuccessfulTransaction $transaction) {
            $products = Product::pluck('id')->toArray();
            if (!empty($products)) {
                $randomProducts = array_rand(array_flip($products), rand(1, min(3, count($products))));
                foreach ((array) $randomProducts as $pid) {
                    $transaction->products()->attach($pid, [
                        'quantity' => rand(1, 10),
                    ]);
                }
            }
        });
    }
}
