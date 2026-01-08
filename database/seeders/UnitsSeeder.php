<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitsSeeder extends Seeder
{
    public function run(): void
    {
        // Generate 300 random units for testing
        Unit::factory()->count(5)->create();
    }
}
