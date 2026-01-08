<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Supplier;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'name'           => $this->faker->company,
            'address'        => $this->faker->address,
            'tax_id'         => $this->faker->unique()->numerify('000-###-###-###'),
            'account_number' => $this->faker->unique()->numerify('##########'),
            'created_at'     => now(),
            'updated_at'     => now(),
        ];
    }
}
