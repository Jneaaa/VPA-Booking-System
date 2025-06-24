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
        DB::table('equipment_categories')->insert([
            [
                'category_name' => 'Audio Equipment',
                'description' => 'Sound systems, microphones, speakers, and audio devices',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_name' => 'Visual Equipment',
                'description' => 'Projectors, screens, displays, and visual presentation devices',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_name' => 'Lighting Equipment',
                'description' => 'Stage lights, spotlights, and lighting systems',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'category_name' => 'Conference Equipment',
                'description' => 'Meeting and conference room equipment and accessories',
                'created_at' => now(),
                'updated_at' => now()
            ]
         ]);
    }
}
