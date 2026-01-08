<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unsuccessful_transactions_to_list', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('unsuccessful_transaction_id');
            $table->decimal('quantity', 8, 2)->unsigned();

            // ✅ Composite primary key
            $table->primary(['product_id', 'unsuccessful_transaction_id'], 'utl_primary');

            // ✅ Foreign keys with cascade delete
            $table->foreign('product_id', 'utl_product_fk')
                ->references('id')->on('products')
                ->onDelete('cascade'); // ✅ When product deleted, delete pivot

            $table->foreign('unsuccessful_transaction_id', 'utl_txn_fk')
                ->references('id')->on('unsuccessful_transactions')
                ->onDelete('cascade'); // ✅ When unsuccessful_transaction deleted, delete pivot
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unsuccessful_transactions_to_list');
    }
};