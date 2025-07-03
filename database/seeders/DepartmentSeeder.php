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
        DB::table('departments')->insert([
            [
                'department_name' => 'College of Agriculture',
                'department_code' => 'COA',
            ],
            [
                'department_name' => 'College of Arts & Sciences',
                'department_code' => 'CAS',
            ],
            [
                'department_name' => 'College of Business & Accountancy',
                'department_code' => 'CBA',
            ],
            [
                'department_name' => 'College of Computer Studies',
                'department_code' => 'CCS',
            ],
            [
                'department_name' => 'College of Education',
                'department_code' => 'COED',
            ],
            [
                'department_name' => 'College of Engineering',
                'department_code' => 'COE',
            ],
            [
                'department_name' => 'College of Hospitality Management',
                'department_code' => 'CHM',
            ],
            [
                'department_name' => 'College of Medical Laboratory Science',
                'department_code' => 'CMLS',
            ],
            [
                'department_name' => 'College of Nursing',
                'department_code' => 'CON',
            ],
            [
                'department_name' => 'College of Pharmacy',
                'department_code' => 'COP',
            ],
            [
                'department_name' => 'College of Theology',
                'department_code' => 'COT',
            ]
         ]);
    }
}
