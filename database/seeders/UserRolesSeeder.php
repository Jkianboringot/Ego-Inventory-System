<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($x=2;$x<=7;$x++){
        DB::table('role_user')->insert([
         [   'role_id'=>$x,
             'user_id'=>$x,
         ]
        ]);
    }
    }
}
