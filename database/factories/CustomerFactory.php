<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $isBusiness = $this->faker->boolean(50); // 50% chance

        if ($isBusiness) {
            $name = $this->faker->company;
            $taxId = 'P' . $this->faker->numerify('#########') . $this->faker->randomLetter;
        } else {
            $name = $this->faker->name;
            $taxId = 'A' . $this->faker->numerify('#########') . $this->faker->randomLetter;
        }

        return [
            'name' => $name,
            'address' => $this->faker->address,
            'tax_id' => $taxId,
        ];
    }
}
