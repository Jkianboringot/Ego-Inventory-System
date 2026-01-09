<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $predefinedUsers = [
            ['name' => 'Joe Smith', 'email' => 'supervisor@gmail.com'],
            ['name' => 'Mark Sulas', 'email' => 'saleclerk@gmail.com'],
            ['name' => 'Loyd Oragon', 'email' => 'inventoryclerk@gmail.com'],
            ['name' => 'Arjey Sigwil', 'email' => 'warehousekeeper@gmail.com'],
            ['name' => 'Jerlyn Fernandex', 'email' => 'returnandexchange@gmail.com'],
        ];

        foreach ($predefinedUsers as $user) {
            User::factory()->create([
                'name' => $user['name'],
                'email' => $user['email'],
            ]);
        }

        // Generate additional 50 random unique users

    }
}
  