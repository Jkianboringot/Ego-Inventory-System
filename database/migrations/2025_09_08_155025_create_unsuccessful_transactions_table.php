<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unsuccessful_transactions', function (Blueprint $table) {
            $table->id();
            
            // // ✅ When order is deleted, delete unsuccessful_transaction
            // $table->foreignId('order_id')
            //     ->nullable()
            //     ->constrained()
            //     ->onDelete('cascade'); // ✅ Add this!
            
            $table->enum('status', ['pending', 'approved', 'rejected', 'edit_pending'])
                ->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unsuccessful_transactions');
    }
};