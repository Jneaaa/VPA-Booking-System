<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('equipment')->insert([
            [
                'equipment_name' => 'Wireless Microphone System',
                'description' => 'Professional wireless microphone system with handheld and lapel mics, suitable for presentations and events',
                'brand' => 'Shure',
                'storage_location' => 'Audio Equipment Room A1',
                'category_id' => 1, // Audio Equipment
                'total_quantity' => 5,
                'rental_fee' => 150.00,
                'company_fee' => 200.00,
                'type_id' => 1, // Per Hour
                'status_id' => 1, // Available
                'department_id' => 3, // College of Computer Studies
                'minimum_hour' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'equipment_name' => 'LCD Projector',
                'description' => 'High-definition LCD projector with 3000 lumens brightness, perfect for presentations and seminars',
                'brand' => 'Epson',
                'storage_location' => 'Visual Equipment Room B2',
                'category_id' => 2, // Visual Equipment
                'total_quantity' => 8,
                'rental_fee' => 100.00,
                'company_fee' => 150.00,
                'type_id' => 1, // Per Hour
                'status_id' => 1, // Available
                'department_id' => 1, // College of Arts & Sciences
                'minimum_hour' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'equipment_name' => 'LED Stage Lights',
                'description' => 'Professional LED stage lighting system with color mixing capabilities for events and performances',
                'brand' => 'Chauvet',
                'storage_location' => 'Lighting Storage Room C1',
                'category_id' => 3, // Lighting Equipment
                'total_quantity' => 12,
                'rental_fee' => 300.00,
                'company_fee' => 450.00,
                'type_id' => 3, // Per Event
                'status_id' => 1, // Available
                'department_id' => 6, // College of Hospitality Management
                'minimum_hour' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ]
         ]);
    }
}
