<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // who approved/rejected

            // Polymorphic relationship
            $table->unsignedBigInteger('approvable_id');
            $table->string('approvable_type');

            // Approval status
            $table->enum('status', ['approved', 'rejected','archived'])->default('approved');

            $table->timestamps();

            $table->index(['approvable_id', 'approvable_type']); // speed up queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
