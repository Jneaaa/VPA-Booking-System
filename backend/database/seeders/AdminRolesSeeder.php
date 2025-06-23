<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admin_roles')->insert([
            [
                'role_id' => 1,
                'role_title' => 'Head Admin',
                'description' => 'Complete system access and administration'
            ],
            [
                'role_id' => 2,
                'role_title' => 'Vice President of Administration',
                'description' => 'View and approve requisition forms'
            ],
            [
                'role_id' => 3,
                'role_title' => 'Signatories',
                'description' => 'Review, approve forms and manage fees'
            ],
            [
                'role_id' => 4,
                'role_title' => 'Inventory Manager',
                'description' => 'Manage facilities, equipment and inventory'
            ]
         ]);
    }
}
