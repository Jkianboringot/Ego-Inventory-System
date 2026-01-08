<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_return', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained();
            $table->foreignId('return_item_id')->constrained('return_items');
            $table->decimal('quantity', 8, 2);
            $table->decimal('unit_price', 8, 2);
            $table->decimal('adds_on', 8, 2)->nullable();
            $table->softDeletes();
            $table->primary(['product_id', 'return_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_return');
    }
};
