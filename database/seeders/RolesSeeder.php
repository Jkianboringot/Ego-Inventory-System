<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'title' => 'Supervisor',
                'permissions' => Json::encode(["Admin", "Supervisor",
                                "Sales Clerk", "Inventory Clerk", "Warehouse Keeper",
                                "Return and Exchange Clerk",'Inventory Clerk']),
                
            ],

            [
                'title' => 'Sales Clerk',
                'permissions' => Json::encode(['Sales Clerk']),


            ],
            [
                'title' => 'Inventory Clerk',
              'permissions' => Json::encode(['Inventory Clerk']),


            ],
            [
                'title' => 'Warehouse Keeper',
               'permissions' => Json::encode(['Warehouse Keeper']),

            ],
            [
                'title' => 'Return and Exchange Clerk',
                  'permissions' => Json::encode(['Return and Exchange Clerk']),


            ],[
             'title' => 'Inventory Clerk',
                  'permissions' => Json::encode(['Inventory Clerk']),
]
        ]);
    }
}
