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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->nullable()->constrained(); 
            $table->foreignId('supplier_id')->constrained();


            $table->foreignId('product_category_id')->nullable()->constrained();
            $table->string('name',75)->unique();
            $table->text('description')->nullable();
             $table->integer('inventory_threshold')->nullable();

            $table->foreignId('unit_id')->constrained();
            $table->decimal('purchase_price', 8, 2)->unsigned();
            $table->decimal('sale_price', 8, 2)->unsigned();


            // $table->string('technical_path')->nullable();
            $table->string('location',20)->nullable();
            $table->string('barcode',30)->nullable()->unique();
$table->softDeletes();



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
