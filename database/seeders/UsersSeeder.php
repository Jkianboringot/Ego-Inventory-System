<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
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
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('123'), // default password
                    'remember_token' => Str::random(10),
                ]
            );
        }

        // Optional: generate additional 50 random users safely
        // User::factory(50)->create();
    }
}
