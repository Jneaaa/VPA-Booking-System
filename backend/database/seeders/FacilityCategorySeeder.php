<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacilityCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('facility_categories')->insert([
            [
                'category_name' => 'Building',
                'description' => 'Standalone structures such as halls and churches.',
            ],
            [
                'category_name' => 'Residential Building',
                'description' => 'Buildings intended for housing, including dormitories.',
            ],
            [
                'category_name' => 'Outside Space',
                'description' => 'Open areas outside of buildings, like gardens and Halfmoon Drive.',
            ],
            [
                'category_name' => 'Room',
                'description' => 'Enclosed indoor spaces such as classrooms and laboratories.',
            ],
            [
                'category_name' => 'Sports Venue',
                'description' => 'Facilities designated for physical activities and sports events.',
            ],
        ]);
    }
}
