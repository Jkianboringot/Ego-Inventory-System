<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->primary(['role_id','user_id']);
   $table->softDeletes();

            $table->timestamps();
        });
        DB::table('users')->insert([
            'id'=>1,    
            'name'=>"Administrator",
            'email'=>'admin@gmail.com', //proabable not safe but it will do now
            'password'=>Hash::make('123')

        ]);
          DB::table('role_user')->insert([
            'role_id'=>1,
            'user_id'=>1,


          ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
