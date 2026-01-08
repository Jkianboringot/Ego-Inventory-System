<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomersSeeder extends Seeder
{
    public function run(): void
    {
        // Create 20 random customers
        Customer::factory()->count(10)->create();
    }
}
