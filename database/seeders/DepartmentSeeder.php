<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // NOTE: Primary key: department_id (auto-increment)
        // NOTE: Timestamps: created_at, updated_at (auto-managed by Laravel)
        
        DB::table('departments')->insert([
            [
                'department_name' => 'Vice President of Administration',
                'department_code' => 'VPA',
            ],
            [
                'department_name' => 'Educational Media Center',
                'department_code' => 'EMC',
            ]
         ]);
    }
}
