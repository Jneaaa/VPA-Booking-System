<?php

namespace Database\Seeders;

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
            ],
            [
                'status_name' => 'Unavailable',
                'color_code' => '#dc3545',
            ],
            [
                'status_name' => 'Under Maintenance',
                'color_code' => '#ffc107',
            ],
            [
                'status_name' => 'Reserved',
                'color_code' => '#007bff',
            ],
            [
                'status_name' => 'Hidden',
                'color_code' => '#343a40',
            ],
         ]);
    }
}
