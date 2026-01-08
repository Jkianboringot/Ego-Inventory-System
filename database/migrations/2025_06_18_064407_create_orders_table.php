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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('orders_ref',22)->unique();


            // $table->boolean('order_status')->default(false); 
            // $table->boolean('return_status')->default(false); 

            // $table->enum('order_status',['successfull','unsucessfull'])->default(false); 

            //need to change this to enum
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->softDeletes();

            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
