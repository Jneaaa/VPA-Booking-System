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
                'internal_fee' => 200.00,
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
                'internal_fee' => 400.00,
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
                'internal_fee' => 50.00,
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
                'internal_fee' => 50.00,
                'external_fee' => 0.00, // Was null in image
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
                'internal_fee' => 100.00,
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
                'internal_fee' => 600.00,
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
                'internal_fee' => 500.00,
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
                'internal_fee' => 800.00,
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
                'internal_fee' => 200.00,
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
                'internal_fee' => 200.00,
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
                'internal_fee' => 1500.00,
                'external_fee' => 0.00, // Was null in image
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
                'internal_fee' => 800.00,
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
                'internal_fee' => 400.00,
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
                'internal_fee' => 100.00,
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
                'internal_fee' => 100.00,
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
                'internal_fee' => 10.00,
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
                'equipment_name' => 'Keyboard',
                'internal_fee' => 100.00,
                'external_fee' => 0.00, // Was null in image
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
                'internal_fee' => 0.00, // Was null in image
                'external_fee' => 0.00, // Was null in image
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
                'internal_fee' => 0.00, // Was null in image
                'external_fee' => 0.00, // Was null in image
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
                'internal_fee' => 3500.00,
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