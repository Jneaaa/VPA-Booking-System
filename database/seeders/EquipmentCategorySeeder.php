<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // NOTE: Primary key: category_id (auto-increment)
        // NOTE: Timestamps: created_at, updated_at (auto-managed by Laravel)

        DB::table('equipment_categories')->insert([
            [
                'category_name' => 'Audio Equipment',
                'description' => 'Sound systems, microphones, speakers, and audio devices',
            ],
            [
                'category_name' => 'Visual Equipment',
                'description' => 'Projectors, screens, displays, and visual presentation devices',
            ],
            [
                'category_name' => 'Lighting Equipment',
                'description' => 'Stage lights, spotlights, and lighting systems',
            ],
            [
                'category_name' => 'Conference Equipment',
                'description' => 'Meeting and conference room equipment and accessories',
            ],
            [
                'category_name' => 'Event Equipment',
                'description' => 'Specialized equipment for events and performances',
            ],
            [
                'category_name' => 'IT Equipment',
                'description' => 'Hardware and devices used for computing and communication',
            ],
         ]);
    }
}
