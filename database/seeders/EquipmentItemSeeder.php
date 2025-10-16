<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('equipment_items')->insert([
            // --- SOUND SYSTEMS ---
            // Equipment ID 1: Sound System (Basic with 2 mics, Player)
            [
                'equipment_id' => 1,
                'item_name' => 'Basic Sound System Unit #001',
                'condition_id' => 2, // Good
                'status_id' => 1, // Available
                'barcode_number' => 'SOUND001',
                'item_notes' => 'Complete basic sound system with 2 mics and player',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'equipment_id' => 1,
                'item_name' => 'Basic Sound System Unit #002',
                'status_id' => 1,
                'condition_id' => 2, // Good
                'barcode_number' => 'SOUND002',
                'item_notes' => 'Backup basic sound system, fully tested',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Equipment ID 2: Sound System (Large with Sub, Digital Mixer, Processors)
            [
                'equipment_id' => 2,
                'item_name' => 'Large Sound System Unit #001',
                'status_id' => 1,
                'condition_id' => 1, // New
                'barcode_number' => 'LSOUND001',
                'item_notes' => 'Professional large sound system with subwoofer and digital mixer',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Equipment ID 3: Additional Speakers
            [
                'equipment_id' => 3,
                'item_name' => 'Additional Speaker Pair A',
                'status_id' => 1,
                'condition_id' => 2, // Good
                'barcode_number' => 'SPKR001',
                'item_notes' => 'Pair of additional speakers, 500W each',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'equipment_id' => 3,
                'item_name' => 'Additional Speaker Pair B',
                'status_id' => 3, // In Use
                'condition_id' => 2, // Good
                'barcode_number' => 'SPKR002',
                'item_notes' => 'Backup speaker pair, currently deployed',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Equipment ID 4: Additional Mics
            [
                'equipment_id' => 4,
                'item_name' => 'Microphone Set A (4 units)',
                'status_id' => 1,
                'condition_id' => 2, // Good
                'barcode_number' => 'MICS001',
                'item_notes' => 'Set of 4 additional microphones',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // --- LIGHTS & EFFECTS ---
            // Equipment ID 5: Lights (RGB Parled with Dimmer)
            [
                'equipment_id' => 5,
                'item_name' => 'RGB Parled Light Set A',
                'status_id' => 1,
                'condition_id' => 2, // Good
                'barcode_number' => 'RGB001',
                'item_notes' => 'Set of 6 RGB Parled lights with dimmer controller',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Equipment ID 6: Moving Heads (with Controller)
            [
                'equipment_id' => 6,
                'item_name' => 'Moving Head Light Pair A',
                'status_id' => 1,
                'condition_id' => 1, // New
                'barcode_number' => 'MOVE001',
                'item_notes' => 'Pair of moving head lights with DMX controller',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Equipment ID 7: Smoke Machine
            [
                'equipment_id' => 7,
                'item_name' => 'Smoke Machine Unit #001',
                'status_id' => 1,
                'condition_id' => 2, // Good
                'barcode_number' => 'SMOKE001',
                'item_notes' => 'Professional smoke machine with remote',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Equipment ID 8: Follow Spot
            [
                'equipment_id' => 8,
                'item_name' => 'Follow Spot Light #001',
                'status_id' => 1,
                'condition_id' => 2, // Good
                'barcode_number' => 'SPOT001',
                'item_notes' => '1000W follow spot light with stand',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // --- VISUAL & CONFERENCE ---
            // Equipment ID 9: Projector (3200 Ansi Lumens)
            [
                'equipment_id' => 9,
                'item_name' => 'Projector Unit #001',
                'status_id' => 1,
                'condition_id' => 2, // Good
                'barcode_number' => 'PROJ001',
                'item_notes' => '3200 Ansi Lumens projector with HDMI cables',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'equipment_id' => 9,
                'item_name' => 'Projector Unit #002',
                'status_id' => 2, // Reserved
                'condition_id' => 2, // Good
                'barcode_number' => 'PROJ002',
                'item_notes' => 'Backup projector unit, reserved for upcoming event',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Equipment ID 10: TV (65 inch)
            [
                'equipment_id' => 10,
                'item_name' => '65-inch TV Unit #001',
                'status_id' => 1,
                'condition_id' => 1, // New
                'barcode_number' => 'TV65001',
                'item_notes' => '65-inch Smart TV with wall mount',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Equipment ID 11: Conference System (16 Delegates)
            [
                'equipment_id' => 11,
                'item_name' => 'Conference System Set A',
                'status_id' => 1,
                'condition_id' => 2, // Good
                'barcode_number' => 'CONF001',
                'item_notes' => '16-delegate conference system with main unit',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // --- MUSICAL INSTRUMENTS & ACCESSORIES ---
            // Equipment ID 12: Drum Set (Yamaha, 6 piece with throne)
            [
                'equipment_id' => 12,
                'item_name' => 'Yamaha Drum Set #001',
                'status_id' => 1,
                'condition_id' => 2, // Good
                'barcode_number' => 'DRUM001',
                'item_notes' => '6-piece Yamaha drum set with throne and cymbals',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Equipment ID 13: Guitar Amplifier (Base, Guitar, Keyboard)
            [
                'equipment_id' => 13,
                'item_name' => 'Guitar Amp Combo A',
                'status_id' => 1,
                'condition_id' => 2, // Good
                'barcode_number' => 'AMP001',
                'item_notes' => 'Multi-purpose amplifier for bass, guitar, and keyboard',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Equipment ID 14: HDMI Splitter and Accessories
            [
                'equipment_id' => 14,
                'item_name' => 'HDMI Splitter Kit A',
                'status_id' => 1,
                'condition_id' => 2, // Good
                'barcode_number' => 'HDMI001',
                'item_notes' => '4-port HDMI splitter with various cables',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Equipment ID 15: Capture Card/Sound Card
            [
                'equipment_id' => 15,
                'item_name' => 'Audio Interface Unit #001',
                'status_id' => 1,
                'condition_id' => 2, // Good
                'barcode_number' => 'AUDIO001',
                'item_notes' => 'USB audio interface with multiple inputs',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Equipment ID 16: Mic Stand
            [
                'equipment_id' => 16,
                'item_name' => 'Mic Stand Set A (3 units)',
                'status_id' => 1,
                'condition_id' => 2, // Good
                'barcode_number' => 'STAND001',
                'item_notes' => 'Set of 3 microphone stands of different heights',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Equipment ID 17: Keyboard
            [
                'equipment_id' => 17,
                'item_name' => 'Digital Keyboard #001',
                'status_id' => 1,
                'condition_id' => 2, // Good
                'barcode_number' => 'KEYS001',
                'item_notes' => '61-key digital keyboard with stand',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Equipment ID 18: Lapel Mic/Headworn Mic
            [
                'equipment_id' => 18,
                'item_name' => 'Lapel Mic Set A (2 units)',
                'status_id' => 1,
                'condition_id' => 2, // Good
                'barcode_number' => 'LAPEL001',
                'item_notes' => 'Set of 2 lapel microphones with transmitters',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Equipment ID 19: Wireless Mic
            [
                'equipment_id' => 19,
                'item_name' => 'Wireless Mic Set A',
                'status_id' => 1,
                'condition_id' => 2, // Good
                'barcode_number' => 'WMIC001',
                'item_notes' => 'Dual wireless microphone system',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Equipment ID 20: Communication System
            [
                'equipment_id' => 20,
                'item_name' => 'Comm System Unit #001',
                'status_id' => 1,
                'condition_id' => 1, // New
                'barcode_number' => 'COMM001',
                'item_notes' => '4-channel communication system with headsets',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}