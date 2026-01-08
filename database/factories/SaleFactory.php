<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        $saleDates = [
            '2024-09-05','2024-09-15','2024-09-22','2024-10-05',
            '2024-11-05','2024-10-15','2024-11-15','2024-10-22',
            '2024-10-28','2024-11-28','2024-10-30','2024-11-30',
        ];

        // Use timestamp + random number to guarantee uniqueness
        $uniqueRef ='S-' . Str::upper(Str::random(8));

        return [
            'sales_ref'   => $uniqueRef,
            'customer_id' => Customer::inRandomOrder()->first()->id,
            'created_at'  => $saleDates[array_rand($saleDates)],
            'updated_at'  => $saleDates[array_rand($saleDates)],
        ];
    }
}
