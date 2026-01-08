<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneSoftDeletes extends Command
{
    protected $signature = 'app:prune-soft-deletes';
    protected $description = 'Permanently delete soft-deleted records older than 60 days';

    public function handle()
    {
        // 60 days
        $cutoff = now()->subSeconds(10); // for testing, keep 10 seconds

        // 1️⃣ DELETE CHILDREN FIRST (pivot table)
        $deletedPivot = DB::table('add_products_to_list')
            ->whereIn('add_product_id', function ($query) use ($cutoff) {
                $query->from('add_products')
                    ->select('id')
                    ->whereNotNull('deleted_at')
                    ->where('deleted_at', '<', $cutoff);
            })
            ->delete();

        // 2️⃣ DELETE THE PARENT
        $deletedProducts = DB::table('add_products')
            ->whereNotNull('deleted_at')
            ->where('deleted_at', '<', $cutoff)
            ->delete();

        $this->info("Deleted pivot rows: $deletedPivot");
        $this->info("Deleted add_products rows: $deletedProducts");
    }
}
