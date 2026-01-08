<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Purchase;

class PurchasesSeeder extends Seeder
{
    public function run(): void
    {
        // Generate 100 random purchases with products attached
        Purchase::factory()->count(100000)->create();
    }
}
