<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class AdminDepartmentSeeder extends Seeder
{
    public function run()
    {
        DB::table('admin_departments')->insert([
            [
                'admin_id' => 1,
                'department_id' => 1,
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'admin_id' => 1,
                'department_id' => 2,
                'is_primary' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'admin_id' => 2,
                'department_id' => 2,
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'admin_id' => 2,
                'department_id' => 3,
                'is_primary' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}