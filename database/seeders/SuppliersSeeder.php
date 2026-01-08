<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SuppliersSeeder extends Seeder
{
    public function run(): void
    {
        // Seed specific suppliers
        $suppliers = [
            ['name' => 'PLDT Inc.'],
            ['name' => 'Philippine Airlines, Inc.'],
            ['name' => 'San Miguel Corporation'],
            ['name' => 'Jollibee Foods Corporation'],
            ['name' => 'ABS-CBN Corporation'],
        ];

        foreach ($suppliers as $s) {
            Supplier::factory()->create(['name' => $s['name']]);
        }

        // Generate 50 random suppliers for testing
        Supplier::factory()->count(10)->create();
    }
}
