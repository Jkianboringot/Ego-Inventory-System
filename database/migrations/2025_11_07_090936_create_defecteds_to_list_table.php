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
        Schema::create('defecteds_to_list', function (Blueprint $table) {
           $table->foreignId('product_id')->constrained();
            $table->foreignId('defected_id')->constrained();
            $table->primary(['product_id','defected_id']);
            $table->decimal('quantity',8,2)->unsigned();
            $table->decimal('unit_price',8,2)->unsigned();
                $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defecteds_to_list');
    }
};
