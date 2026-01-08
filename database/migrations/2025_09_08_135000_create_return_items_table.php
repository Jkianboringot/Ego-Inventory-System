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
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('sale_id')->nullable()->constrained();


            $table->enum('return_type', ['refunded', 'exchanged'])->default('exchanged');

            $table->enum('status', ['pending', 'approved', 'rejected', 'edit_pending'])->default('pending');

            $table->string('reason');

            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_items');
    }
};
