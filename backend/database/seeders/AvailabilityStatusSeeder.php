<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AvailabilityStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('availability_statuses')->insert([
            [
                'status_name' => 'Available',
                'color_code' => '#28a745',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'status_name' => 'Unavailable',
                'color_code' => '#dc3545',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'status_name' => 'Under Maintenance',
                'color_code' => '#ffc107',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'status_name' => 'Closed',
                'color_code' => '#6c757d',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'status_name' => 'Hidden',
                'color_code' => '#343a40',
                'created_at' => now(),
                'updated_at' => now()
            ]
         ]);
    }
}
