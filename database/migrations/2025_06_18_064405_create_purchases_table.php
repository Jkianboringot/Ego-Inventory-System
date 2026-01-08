<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
        $table->foreignId('supplier_id')->nullable()->constrained();
            $table->date('date_settled')->nullable();
            $table->enum('is_paid',['Paid','Unpaid','Partially_Paid']);//change this to enum in the future
                //because using string cause more storage and harder to query


             $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
