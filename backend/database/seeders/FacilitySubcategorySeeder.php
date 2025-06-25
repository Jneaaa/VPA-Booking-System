<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacilitySubcategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('facility_subcategories')->insert([

            // Building Subcategories
            ['category_id' => 1, 'subcategory_name' => 'Hall'],
            ['category_id' => 1, 'subcategory_name' => 'Building'],
            ['category_id' => 1, 'subcategory_name' => 'Church'],

            // Room Subcategories
            ['category_id' => 4, 'subcategory_name' => 'Academic Room'],
            ['category_id' => 4, 'subcategory_name' => 'Conference Room'],
            ['category_id' => 4, 'subcategory_name' => 'Dorm Room'],
            ['category_id' => 4, 'subcategory_name' => 'Computer Lab'],
            ['category_id' => 4, 'subcategory_name' => 'Laboratory'],

            // Sports Venue Subcategories
            ['category_id' => 5, 'subcategory_name' => 'Stadium'],
            ['category_id' => 5, 'subcategory_name' => 'Gymnasium'],
            ['category_id' => 5, 'subcategory_name' => 'Tennis Court'],
            ['category_id' => 5, 'subcategory_name' => 'Football Field'],
            ['category_id' => 5, 'subcategory_name' => 'Basketball Court'],
            ['category_id' => 5, 'subcategory_name' => 'Swimming Pool'],
            ['category_id' => 5, 'subcategory_name' => 'Field'],
            ['category_id' => 5, 'subcategory_name' => 'Court'],
            ['category_id' => 5, 'subcategory_name' => 'Gym'],
            
        ]);
    }
}
