<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('conditions')->insert([
            [
                'condition_name' => 'New',
                'color_code' => '#28a745',
            ],
            [
                'condition_name' => 'Good',
                'color_code' => '#20c997',
            ],
            [
                'condition_name' => 'Fair',
                'color_code' => '#ffc107',
            ],
            [
                'condition_name' => 'Needs Maintenance',
                'color_code' => '#fd7e14',
            ],
            [
                'condition_name' => 'Damaged',
                'color_code' => '#dc3545',
            ],
            [
                'condition_name' => 'In Use',
                'color_code' => '#6f42c1',
            ]
         ]);
    }
}
