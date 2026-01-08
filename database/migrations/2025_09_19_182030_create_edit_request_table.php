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
       Schema::create('edit_requests', function (Blueprint $table) {
             $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // who requested the edit

            // Polymorphic relationship
            $table->unsignedBigInteger('editable_id');   // e.g. id of AddProduct, ReturnItem, etc.
            $table->string('editable_type');              // e.g. App\Models\AddProduct

            $table->json('changes');                      // proposed edits
            $table->enum('status', ['pending', 'approved', 'rejected','archived','edit_pending'])->default('pending');

            $table->timestamps();

            $table->index(['editable_id', 'editable_type']); // improves query speed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
           Schema::dropIfExists('edit_requests');
    }
};
