<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->insert([
            [
                'department_name' => 'College of Agriculture',
                'department_code' => 'COA',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'department_name' => 'College of Arts & Sciences',
                'department_code' => 'CAS',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'department_name' => 'College of Business & Accountancy',
                'department_code' => 'CBA',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'department_name' => 'College of Computer Studies',
                'department_code' => 'CCS',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'department_name' => 'College of Education',
                'department_code' => 'COED',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'department_name' => 'College of Engineering',
                'department_code' => 'COE',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'department_name' => 'College of Hospitality Management',
                'department_code' => 'CHM',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'department_name' => 'College of Medical Laboratory Science',
                'department_code' => 'CMLS',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'department_name' => 'College of Nursing',
                'department_code' => 'CON',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'department_name' => 'College of Pharmacy',
                'department_code' => 'COP',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'department_name' => 'College of Theology',
                'department_code' => 'COT',
                'created_at' => now(),
                'updated_at' => now()
            ]
         ]);
    }
}
