<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImageTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('image_types')->insert([
            [
                'type_name' => 'Primary',
                'description' => 'Cover photo for inventory item, displayed in booking catalog as main image',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'type_name' => 'Additional',
                'description' => 'Part of image carousel when users browse catalog item details',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
            ]);
            
            foreach ($imageTypes as $type) {
                ImageType::firstOrCreate(
                    ['name' => $type['name']],
                    $type
                );
            }
        }
    }

