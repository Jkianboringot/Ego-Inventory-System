<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Create 10000 orders with products attached
        Order::factory()->count(50000)->create();
    }
}
