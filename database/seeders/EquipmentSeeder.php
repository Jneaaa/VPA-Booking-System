<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentSeeder extends Seeder
{
      public function run()
    {
        DB::table('equipment')->insert([
            // --- SOUND SYSTEMS ---
            [
                'equipment_name' => 'Sound System (Basic with 2 mics, Player)',
                'external_fee' => 5000.00,
                'rate_type' => 'Per Hour',
                'category_id' => 1, // Please change to your actual category ID
                'status_id' => 1,   // Please change to your actual status ID
                'department_id' => 1, // Please change to your actual department ID
                'created_by' => 1, // Please change to an existing admin_id
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Sound System (Large with Sub, Digital Mixer, Processors)',
                'external_fee' => 10000.00,
                'rate_type' => 'Per Hour',
                'category_id' => 1,
                'status_id' => 1,
                'department_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Additional Speakers',
                'external_fee' => 5000.00,
                'rate_type' => 'Per Hour',
                'category_id' => 1,
                'status_id' => 1,
                'department_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Additional Mics',
                'external_fee' => 700.00, // Was null in image
                'rate_type' => 'Per Hour',
                'category_id' => 1,
                'status_id' => 1,
                'department_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // --- LIGHTS & EFFECTS ---
            [
                'equipment_name' => 'Lights (RGB Parled with Dimmer)',
                'external_fee' => 600.00,
                'rate_type' => 'Per Event', // Was 'Per Piece, Show'
                'category_id' => 2, // Assuming new category
                'status_id' => 1,
                'department_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Moving Heads (with Controller)',
                'external_fee' => 1200.00,
                'rate_type' => 'Per Event', // Was 'Per Piece, Show'
                'category_id' => 2,
                'status_id' => 1,
                'department_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Smoke Machine',
                'external_fee' => 600.00,
                'rate_type' => 'Per Event', // Was 'Piece, Show'
                'category_id' => 2,
                'status_id' => 1,
                'department_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Follow Spot',
                'external_fee' => 1500.00,
                'rate_type' => 'Per Event', // Was 'Show'
                'category_id' => 2,
                'status_id' => 1,
                'department_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // --- VISUAL & CONFERENCE ---
            [
                'equipment_name' => 'Projector (3200 Ansi Lumens)',
                'external_fee' => 5000.00,
                'rate_type' => 'Per Hour', // Default
                'category_id' => 3, // Assuming new category
                'status_id' => 1,
                'department_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'TV (65 inch)',
                'external_fee' => 0.00, // Was null in image
                'rate_type' => 'Per Hour', // Default
                'category_id' => 3,
                'status_id' => 1,
                'department_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Conference System (16 Delegates)',
                'external_fee' => 200.00, // Was null in image
                'rate_type' => 'Per Event',
                'category_id' => 3,
                'status_id' => 1,
                'department_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // --- MUSICAL INSTRUMENTS & ACCESSORIES ---
            [
                'equipment_name' => 'Drum Set (Yamaha, 6 piece with throne)',
                'external_fee' => 3500.00,
                'rate_type' => 'Per Event', // Was 'Per Show'
                'category_id' => 4, // Assuming new category
                'status_id' => 1,
                'department_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Guitar Amplifier (Base, Guitar, Keyboard)',
                'external_fee' => 3000.00,
                'rate_type' => 'Per Event', // Was 'Per Show'
                'category_id' => 4,
                'status_id' => 1,
                'department_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'HDMI Splitter and Accessories',
                'external_fee' => 0.00, // Was null in image
                'rate_type' => 'Per Event',
                'category_id' => 5, // Assuming new category
                'status_id' => 1,
                'department_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Capture Card/Sound Card',
                'external_fee' => 0.00, // Was null in image
                'rate_type' => 'Per Event',
                'category_id' => 5,
                'status_id' => 1,
                'department_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Mic Stand',
                'external_fee' => 100.00, // Was null in image
                'rate_type' => 'Per Event',
                'category_id' => 5,
                'status_id' => 1,
                'department_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Keyboard',
                'external_fee' => 100.00, // Was null in image
                'rate_type' => 'Per Event',
                'category_id' => 4,
                'status_id' => 1,
                'department_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Lapel Mic/Hedworn Mic',
                'external_fee' => 100.00, // Was null in image
                'rate_type' => 'Per Hour', // Default
                'category_id' => 1,
                'status_id' => 1,
                'department_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Wireless Mic',
                'external_fee' => 100.00, // Was null in image
                'rate_type' => 'Per Hour', // Default
                'category_id' => 1,
                'status_id' => 1,
                'department_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Communication System',
                'external_fee' => 5000.00,
                'rate_type' => 'Per Event', // Was 'Per Show'
                'category_id' => 1,
                'status_id' => 1,
                'department_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}