<?php

namespace Database\Seeders;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserRolesSeeder extends Seeder
{
    public function run(): void
    {
        $roleUserMap = [
            'supervisor@gmail.com' => 'Supervisor',
            'saleclerk@gmail.com' => 'Sales Clerk',
            'inventoryclerk@gmail.com' => 'Inventory Clerk',
            'warehousekeeper@gmail.com' => 'Warehouse Keeper',
            'returnandexchange@gmail.com' => 'Return and Exchange Clerk',
        ];

        foreach ($roleUserMap as $email => $roleTitle) {
            $user = User::where('email', $email)->first();
            $role = Role::where('title', $roleTitle)->first();

            if ($user && $role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }
        }
    }
}
