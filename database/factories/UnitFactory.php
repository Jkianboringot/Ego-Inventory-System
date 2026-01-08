<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Unit;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Unit>
 */
class UnitFactory extends Factory
{
    protected $model = Unit::class;

    public function definition(): array
    {
        $unitNames = [
            'Kilogram', 'Gram', 'Metric Ton', 'Liter', 'Milliliter',
            'Piece', 'Dozen', 'Box', 'Pack', 'Centimeter',
            'Square Meter', 'Square Foot', 'Millimeter'
        ];

        $unitSymbols = [
            'kg', 'g', 't', 'L', 'mL',
            'pc', 'dz', 'box', 'pk', 'cm',
            'm²', 'ft²', 'mm'
        ];

        $index = $this->faker->numberBetween(0, count($unitNames) - 1);

        return [
            'name'   => $unitNames[$index] . ' ' . $this->faker->unique()->word(), // ensure uniqueness
           'symbol' => substr($unitSymbols[$index] . ' ' . $this->faker->unique()->word(), 0, 5),

        ];
    }
}
