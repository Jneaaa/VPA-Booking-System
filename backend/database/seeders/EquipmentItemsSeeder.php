<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        DB::table('equipment_items')->insert([
            // Wireless Microphone System items
            [
                'equipment_id' => 1,
                'item_name' => 'Wireless Mic System Unit #001',
                'condition_id' => 1, // New
                'barcode_number' => 'WMS001',
                'item_notes' => 'Complete set with handheld and lapel mics, receiver, and charging dock',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'equipment_id' => 1,
                'item_name' => 'Wireless Mic System Unit #002',
                'condition_id' => 2, // Good
                'barcode_number' => 'WMS002',
                'item_notes' => 'Minor wear on handheld mic casing, fully functional',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'equipment_id' => 1,
                'item_name' => 'Wireless Mic System Unit #003',
                'condition_id' => 4, // Needs Maintenance
                'barcode_number' => 'WMS003',
                'item_notes' => 'Battery compartment needs cleaning, lapel mic cable loose',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
         
            // LCD Projector items
            [
                'equipment_id' => 2,
                'item_name' => 'LCD Projector Unit #001',
                'condition_id' => 1, // New
                'barcode_number' => 'PROJ001',
                'item_notes' => 'Brand new unit with original packaging and cables',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'equipment_id' => 2,
                'item_name' => 'LCD Projector Unit #002',
                'condition_id' => 2, // Good
                'barcode_number' => 'PROJ002',
                'item_notes' => 'Regularly serviced, lamp at 80% life remaining',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'equipment_id' => 2,
                'item_name' => 'LCD Projector Unit #003',
                'condition_id' => 6, // In Use
                'barcode_number' => 'PROJ003',
                'item_notes' => 'Currently deployed in Conference Room B for ongoing seminar',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
         
            // LED Stage Lights items
            [
                'equipment_id' => 3,
                'item_name' => 'LED Stage Light Set A (4 units)',
                'condition_id' => 1, // New
                'barcode_number' => 'LED001',
                'item_notes' => 'Complete set with DMX controller and mounting brackets',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'equipment_id' => 3,
                'item_name' => 'LED Stage Light Set B (4 units)',
                'condition_id' => 2, // Good
                'barcode_number' => 'LED002',
                'item_notes' => 'One unit has slight color inconsistency but functional',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'equipment_id' => 3,
                'item_name' => 'LED Stage Light Set C (4 units)',
                'condition_id' => 3, // Fair
                'barcode_number' => 'LED003',
                'item_notes' => 'Some wear on housing, all lights working properly',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
         ]);
    }
}
