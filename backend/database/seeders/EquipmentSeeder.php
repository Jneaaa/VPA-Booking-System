<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('equipment')->insert([
            [
                'equipment_name' => 'Wireless Microphone System',
                'description' => 'Professional wireless microphone system with handheld and lapel mics, suitable for presentations and events.',
                'brand' => 'Shure',
                'storage_location' => 'Equipment Room A',
                'category_id' => 1, // Audio Equipment
                'total_quantity' => 5,
                'rental_fee' => 150.00,
                'company_fee' => 450.00,
                'type_id' => 1, // e.g., Per Hour
                'status_id' => 1,
                'department_id' => 3,
                'minimum_hour' => 2,
                'created_by' => 1, // Assuming admin ID 1 is the creator
                'created_at' => now(),
            ],
            [
                'equipment_name' => 'LCD Projector',
                'description' => 'High-definition LCD projector with 3000 lumens brightness, perfect for presentations and seminars',
                'storage_location' => 'Equipment Room B',
                'brand' => 'Epson',
                'category_id' => 2, // Visual Equipment
                'total_quantity' => 8,
                'rental_fee' => 100.00,
                'company_fee' => 450.00,
                'type_id' => 1, // e.g., Per Hour
                'status_id' => 1,
                'department_id' => 1,
                'minimum_hour' => 1,
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'equipment_name' => 'LED Stage Lights',
                'description' => 'Professional LED stage lighting system with color mixing capabilities for events and performances.',
                'brand' => 'Chauvet',
                'storage_location' => 'Lighting Storage Room C1',
                'category_id' => 3,
                'total_quantity' => 12,
                'rental_fee' => 300.00,
                'company_fee' => 450.00,
                'type_id' => 2, // Per Event
                'status_id' => 1,
                'department_id' => 6,
                'minimum_hour' => 4,
                'created_by' => 1,
                'created_at' => now(),
            ]
        ]);
    }
}
