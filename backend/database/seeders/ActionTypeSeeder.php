<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('action_types')->insert([
            [
                'action_name' => 'Created Equipment',
                'description' => 'Added new equipment to inventory',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Edited Equipment',
                'description' => 'Modified equipment details',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Deleted Equipment',
                'description' => 'Removed equipment from inventory',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Created Facility',
                'description' => 'Added new facility to inventory',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Edited Facility',
                'description' => 'Modified facility details',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Deleted Facility',
                'description' => 'Removed facility from inventory',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Rejected Form',
                'description' => 'Rejected a requisition form',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Approved Form',
                'description' => 'Approved a requisition form',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Finalized Form',
                'description' => 'Finalized a requisition form',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Added Calendar Event',
                'description' => 'Created new event in calendar',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Changed Admin Role',
                'description' => 'Modified admin user role',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Changed Equipment Condition',
                'description' => 'Updated equipment condition status',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Changed Rental Fee',
                'description' => 'Modified facility/equipment rental fee',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Changed Company Fee',
                'description' => 'Modified facility/equipment company fee',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Created Admin',
                'description' => 'Added new admin user to system',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Deleted Admin',
                'description' => 'Removed admin user from system',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Edited Admin',
                'description' => 'Modified admin user details',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Added Misc Fee',
                'description' => 'Added miscellaneous fee to form',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Waived Fees',
                'description' => 'Waived fees from a form',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Added Discount',
                'description' => 'Applied discount to a form',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Added Remarks',
                'description' => 'Added remarks/notes to a form',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Marked Form Completed',
                'description' => 'Marked form as completed or closed',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'action_name' => 'Added Late Penalty',
                'description' => 'Applied late penalty fee to form',
                'created_at' => now(),
                'updated_at' => now()
            ]
         ]);
    }
}
