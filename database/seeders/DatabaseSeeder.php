<?php

namespace Database\Seeders;

use App\Models\ReturnItem;
use App\Models\UnsuccessfulTransaction;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,

            UsersSeeder::class,
            UserRolesSeeder::class,
            
           
            

        ]);
    }
}