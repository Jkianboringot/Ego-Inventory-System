<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\UnsuccessfulTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnsucessfullTransactionSeeder extends Seeder
{
    public function run(): void
    {
        UnsuccessfulTransaction::factory()->count(100000)->create();
    }
}
