<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['title' => 'Supervisor', 'permissions' => ['Admin','Supervisor','Sales Clerk','Inventory Clerk','Warehouse Keeper','Return and Exchange Clerk']],
            ['title' => 'Sales Clerk', 'permissions' => ['Sales Clerk']],
            ['title' => 'Inventory Clerk', 'permissions' => ['Inventory Clerk']],
            ['title' => 'Warehouse Keeper', 'permissions' => ['Warehouse Keeper']],
            ['title' => 'Return and Exchange Clerk', 'permissions' => ['Return and Exchange Clerk']],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['title' => $role['title']],
                ['permissions' => json_encode($role['permissions'])]
            );
        }
    }
}
