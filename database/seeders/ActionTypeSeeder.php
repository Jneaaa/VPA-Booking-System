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
            ],
            [
                'action_name' => 'Edited Equipment',
                'description' => 'Modified equipment details',
            ],
            [
                'action_name' => 'Deleted Equipment',
                'description' => 'Removed equipment from inventory',
            ],
            [
                'action_name' => 'Created Facility',
                'description' => 'Added new facility to inventory',
            ],
            [
                'action_name' => 'Edited Facility',
                'description' => 'Modified facility details',
            ],
            [
                'action_name' => 'Deleted Facility',
                'description' => 'Removed facility from inventory',
            ],
            [
                'action_name' => 'Rejected Form',
                'description' => 'Rejected a requisition form',
            ],
            [
                'action_name' => 'Approved Form',
                'description' => 'Approved a requisition form',
            ],
            [
                'action_name' => 'Finalized Form',
                'description' => 'Finalized a requisition form',
            ],
            [
                'action_name' => 'Added Calendar Event',
                'description' => 'Created new event in calendar',
            ],
            [
                'action_name' => 'Changed Admin Role',
                'description' => 'Modified admin user role',
            ],
            [
                'action_name' => 'Changed Equipment Condition',
                'description' => 'Updated equipment condition status',
            ],
            [
                'action_name' => 'Changed Facility Fee',
                'description' => 'Modified facility rental fee',
            ],
                        [
                'action_name' => 'Changed Equipment Fee',
                'description' => 'Modified equipment rental fee',
            ],
            [
                'action_name' => 'Created Admin',
                'description' => 'Added new admin user to system',
            ],
            [
                'action_name' => 'Deleted Admin',
                'description' => 'Removed admin user from system',
            ],
            [
                'action_name' => 'Edited Admin',
                'description' => 'Modified admin user details',
            ],
            [
                'action_name' => 'Added Misc Fee',
                'description' => 'Added miscellaneous fee to form',
            ],
            [
                'action_name' => 'Waived Fees',
                'description' => 'Waived fees from a form',
            ],
            [
                'action_name' => 'Added Discount',
                'description' => 'Applied discount to a form',
            ],
            [
                'action_name' => 'Added Remarks',
                'description' => 'Added remarks/notes to a form',
            ],
            [
                'action_name' => 'Marked Form Completed',
                'description' => 'Marked form as completed or closed',
            ],
            [
                'action_name' => 'Added Late Penalty',
                'description' => 'Applied late penalty fee to form',
            ]
         ]);
    }
}
