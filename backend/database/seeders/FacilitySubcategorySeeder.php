<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacilitySubcategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('facility_subcategories')->insert([

            // Buildings 
            ['category_id' => 1, 'subcategory_name' => 'Event Halls'],
            ['category_id' => 1, 'subcategory_name' => 'Academic Buildings'],
            ['category_id' => 1, 'subcategory_name' => 'Worship Spaces'],

            // Indoor Facilities 
            ['category_id' => 2, 'subcategory_name' => 'Classrooms'],
            ['category_id' => 2, 'subcategory_name' => 'Conference Rooms'],
            ['category_id' => 2, 'subcategory_name' => 'Computer Laboratories'],
            ['category_id' => 2, 'subcategory_name' => 'Science Laboratories'],

            // Residencies
            ['category_id' => 3, 'subcategory_name' => 'Dormitories'],
            ['category_id' => 3, 'subcategory_name' => 'Apartments'],
            ['category_id' => 3, 'subcategory_name' => 'Dormitory Rooms'],
            ['category_id' => 3, 'subcategory_name' => 'Staff Quarters'],
            ['category_id' => 3, 'subcategory_name' => 'Guest Rooms'],
            
            // Outside Spaces
            ['category_id' => 4, 'subcategory_name' => 'Gardens'],
            ['category_id' => 4, 'subcategory_name' => 'Open Fields'],
            ['category_id' => 4, 'subcategory_name' => 'Promenades'],
            ['category_id' => 4, 'subcategory_name' => 'Courtyards'],

            // Sports Venues
            ['category_id' => 5, 'subcategory_name' => 'Gymnasiums'],
            ['category_id' => 5, 'subcategory_name' => 'Indoor Courts'],
            ['category_id' => 5, 'subcategory_name' => 'Outdoor Courts'],
            ['category_id' => 5, 'subcategory_name' => 'Swimming Pools'],
            ['category_id' => 5, 'subcategory_name' => 'Sports Fields'],
            
        ]);
    }
}
