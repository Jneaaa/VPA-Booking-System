<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImageTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $imageTypes = [
            [
                'name' => 'Primary',
                'description' => 'Main cover photo for carousel',
                'is_active' => true
            ],
            [
                'name' => 'Additional',
                'description' => 'Additional photos for carousel',
                'is_active' => true
            ]
        ];

        foreach ($imageTypes as $type) {
            ImageType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
