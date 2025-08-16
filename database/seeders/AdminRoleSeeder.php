<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminRoleSeeder extends Seeder
{

    public function run(): void
    {
        DB::table('admin_roles')->insert([
            [
                'role_id' => 1,
                'role_title' => 'Head Admin',
                'description' => 'Complete system access and administration, including adding new admins.'
            ],
            [
                'role_id' => 2,
                'role_title' => 'Vice President of Administration',
                'description' => 'View and approve requisition forms only.'
            ],
            [
                'role_id' => 3,
                'role_title' => 'Approving Officer',
                'description' => 'Manage and review forms, equipment, and facilities.'
            ],
            [
                'role_id' => 4,
                'role_title' => 'Inventory Manager',
                'description' => 'Manage facilities & equipment only.'
            ]
         ]);
    }
}
