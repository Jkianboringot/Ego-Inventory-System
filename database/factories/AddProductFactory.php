<?php

namespace Database\Factories;

use App\Models\AddProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AddProduct>
 */
class AddProductFactory extends Factory
{
    protected $model = AddProduct::class;

    public function definition(): array
    {
        $statuses = ['pending', 'pending', 'pending', 'rejected', 'approved'];

        return [
            'status' => $statuses[array_rand($statuses)],
        ];
    }
}
