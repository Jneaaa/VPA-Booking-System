<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RequisitionFormsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('requisition_forms')->insert([
            'user_type' => 'External',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'school_id' => null,
            'organization_name' => 'Sample Organization',
            'contact_number' => '09171234567',

            'access_code' => strtoupper(Str::random(8)),
            'num_participants' => 25,
            'purpose_id' => 1, // make sure this exists in requisition_purposes
            'additional_requests' => 'Need extra chairs and microphones',

            'formal_letter_url' => 'https://example.com/formal_letter.pdf',
            'formal_letter_public_id' => 'formal_letter_123',
            'facility_layout_url' => null,
            'facility_layout_public_id' => null,
            'proof_of_payment_url' => null,
            'proof_of_payment_public_id' => null,
            'upload_token' => Str::random(20),

            'status_id' => 1, // make sure this exists in form_statuses

            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',

            // late return details
            'is_late' => true,
            'returned_at' => now()->addDays(3),

            'is_finalized' => false,
            'finalized_at' => null,
            'finalized_by' => null,

            'tentative_fee' => 5000.00,
            'approved_fee' => null,

            'is_closed' => false,
            'closed_at' => null,
            'closed_by' => null,

            'endorser' => null,
            'date_endorsed' => null,

            'created_at' => now(),
            'updated_at' => now(),
        ]);

         // Insert requested facilities for this requisition form
        DB::table('requested_facilities')->insert([
            [
                'request_id' => 1,
                'facility_id' => 1, // must exist in facilities table
            ],
            [
                'request_id' => 1,
                'facility_id' => 2, // another facility
            ],
        ]);
        DB::table('requisition_forms')->insert([
    'user_type' => 'Internal',
    'first_name' => 'Maria',
    'last_name' => 'Santos',
    'email' => 'maria.santos@university.edu',
    'school_id' => '202512345',
    'organization_name' => null,
    'contact_number' => '09179876543',

    'access_code' => strtoupper(Str::random(8)),
    'num_participants' => 40,
    'purpose_id' => 2, // make sure this exists in requisition_purposes
    'additional_requests' => 'Projector and whiteboard needed',

    'formal_letter_url' => 'https://example.com/formal_letter_alex.pdf',
    'formal_letter_public_id' => 'letter_alex',
    'facility_layout_url' => 'https://example.com/layout.pdf',
    'facility_layout_public_id' => 'layout_456',
    'proof_of_payment_url' => 'https://example.com/payment.pdf',
    'proof_of_payment_public_id' => 'payment_456',
    'upload_token' => Str::random(20),

    'status_id' => 2, // make sure this exists in form_statuses

    'start_date' => now()->addDays(5)->toDateString(),
    'end_date' => now()->addDays(5)->toDateString(),
    'start_time' => '13:00:00',
    'end_time' => '16:00:00',

    'is_late' => false,
    'returned_at' => null,

    'is_finalized' => false,
    'finalized_at' => null,
    'finalized_by' => null,

    'tentative_fee' => 3500.00,
    'approved_fee' => null,

    'is_closed' => false,
    'closed_at' => null,
    'closed_by' => null,

    'endorser' => 'Dr. Reyes',
    'date_endorsed' => now()->addDay(),

    'created_at' => now(),
    'updated_at' => now(),
]);

// Insert requested facilities for this new requisition form
DB::table('requested_facilities')->insert([
    [
        'request_id' => 2, // match this to the new form's ID
        'facility_id' => 3,
    ],
    [
        'request_id' => 2,
        'facility_id' => 4,
    ],
]);

DB::table('requisition_forms')->insert([
    'user_type' => 'External',
    'first_name' => 'Alex',
    'last_name' => 'Tan',
    'email' => 'alex.tan@example.com',
    'school_id' => null,
    'organization_name' => 'Tech Innovators Club',
    'contact_number' => '09221234567',

    'access_code' => strtoupper(Str::random(8)),
    'num_participants' => 15,
    'purpose_id' => 3, // ensure this exists in requisition_purposes
    'additional_requests' => 'Need extra tables and projector',

    'formal_letter_url' => 'https://example.com/formal_letter_alex.pdf',
    'formal_letter_public_id' => 'formal_letter_alex',
    'facility_layout_url' => null,
    'facility_layout_public_id' => null,
    'proof_of_payment_url' => null,
    'proof_of_payment_public_id' => null,
    'upload_token' => Str::random(20),

    'status_id' => 3, // specific status
    'start_date' => now()->addDays(7)->toDateString(),
    'end_date' => now()->addDays(7)->toDateString(),
    'start_time' => '10:00:00',
    'end_time' => '15:00:00',

    'is_late' => false,
    'returned_at' => null,

    'is_finalized' => true,
    'finalized_at' => now(),
    'finalized_by' => 1, // admin ID who finalized

    'tentative_fee' => 4500.00,
    'approved_fee' => 4500.00,

    'is_closed' => false,
    'closed_at' => null,
    'closed_by' => null,

    'endorser' => 'Dr. Cruz',
    'date_endorsed' => now()->addDays(1),

    'created_at' => now(),
    'updated_at' => now(),
]);

// Insert requested facilities for this new requisition form
DB::table('requested_facilities')->insert([
    [
        'request_id' => 3, // match this to the new form's ID
        'facility_id' => 2,
    ],
    [
        'request_id' => 3,
        'facility_id' => 5,
    ],
]);

// Optional: insert requested equipment
DB::table('requested_equipment')->insert([
    [
        'request_id' => 3,
        'equipment_id' => 1, // must exist in equipment table
        'quantity' => 2,
    ],
    [
        'request_id' => 3,
        'equipment_id' => 3,
        'quantity' => 1,
    ],
]);
DB::table('requisition_forms')->insert([
    'user_type' => 'Internal',
    'first_name' => 'Maria',
    'last_name' => 'Lopez',
    'email' => 'maria.lopez@example.com',
    'school_id' => '20231234',
    'organization_name' => 'Student Council',
    'contact_number' => '09181234567',

    'access_code' => strtoupper(Str::random(8)),
    'num_participants' => 30,
    'purpose_id' => 2, // ensure this exists in requisition_purposes
    'additional_requests' => 'Include sound system and banners',

    'formal_letter_url' => 'https://example.com/formal_letter_maria.pdf',
    'formal_letter_public_id' => 'formal_letter_maria',
    'facility_layout_url' => null,
    'facility_layout_public_id' => null,
    'proof_of_payment_url' => 'https://example.com/payment_proof_maria.pdf',
    'proof_of_payment_public_id' => 'payment_proof_maria',
    'upload_token' => Str::random(20),

    'status_id' => 4, // specific status
    'start_date' => now()->subDays(2)->toDateString(),
    'end_date' => now()->subDays(2)->toDateString(),
    'start_time' => '08:00:00',
    'end_time' => '12:00:00',

    'is_late' => false,
    'returned_at' => now()->subDays(2)->addHours(1),

    'is_finalized' => true,
    'finalized_at' => now()->subDays(3),
    'finalized_by' => 2, // admin ID

    'tentative_fee' => 6000.00,
    'approved_fee' => 6000.00,

    'is_closed' => true,
    'closed_at' => now()->subDays(1),
    'closed_by' => 2, // admin ID

    'endorser' => 'Prof. Santos',
    'date_endorsed' => now()->subDays(4),

    'created_at' => now()->subDays(5),
    'updated_at' => now()->subDays(1),
]);

// Insert requested facilities for this requisition form
DB::table('requested_facilities')->insert([
    [
        'request_id' => 4, // match the new form's ID
        'facility_id' => 1,
    ],
    [
        'request_id' => 4,
        'facility_id' => 3,
    ],
]);

// Insert requested equipment
DB::table('requested_equipment')->insert([
    [
        'request_id' => 4,
        'equipment_id' => 2,
        'quantity' => 1,
    ],
    [
        'request_id' => 4,
        'equipment_id' => 4,
        'quantity' => 3,
    ],
]);
DB::table('requisition_forms')->insert([
    'user_type' => 'External',
    'first_name' => 'Carlos',
    'last_name' => 'Gomez',
    'email' => 'carlos.gomez@example.com',
    'school_id' => null,
    'organization_name' => 'Community Group',
    'contact_number' => '09221234567',

    'access_code' => strtoupper(Str::random(8)),
    'num_participants' => 15,
    'purpose_id' => 3, // make sure this exists in requisition_purposes
    'additional_requests' => 'Projector and seating for participants',

    'formal_letter_url' => 'https://example.com/formal_letter_carlos.pdf',
    'formal_letter_public_id' => 'formal_letter_carlos',
    'facility_layout_url' => null,
    'facility_layout_public_id' => null,
    'proof_of_payment_url' => null,
    'proof_of_payment_public_id' => null,
    'upload_token' => Str::random(20),

    'status_id' => 5, // Cancelled/Rejected
    'start_date' => now()->addDays(5)->toDateString(),
    'end_date' => now()->addDays(5)->toDateString(),
    'start_time' => '13:00:00',
    'end_time' => '16:00:00',

    'is_late' => false,
    'returned_at' => null,

    'is_finalized' => false,
    'finalized_at' => null,
    'finalized_by' => null,

    'tentative_fee' => 2500.00,
    'approved_fee' => null,

    'is_closed' => true,
    'closed_at' => now()->addDays(6),
    'closed_by' => 1, // admin ID

    'endorser' => null,
    'date_endorsed' => null,

    'created_at' => now(),
    'updated_at' => now(),
]);

// Insert requested facilities for this requisition form
DB::table('requested_facilities')->insert([
    [
        'request_id' => 5, // match the new form's ID
        'facility_id' => 2,
    ]
]);

// Insert requested equipment
DB::table('requested_equipment')->insert([
    [
        'request_id' => 5,
        'equipment_id' => 3,
        'quantity' => 2,
    ]
]);
DB::table('requisition_forms')->insert([
    'user_type' => 'Internal',
    'first_name' => 'Maria',
    'last_name' => 'Lopez',
    'email' => 'maria.lopez@example.com',
    'school_id' => 'INT2025-001',
    'organization_name' => 'University Club',
    'contact_number' => '09331234567',

    'access_code' => strtoupper(Str::random(8)),
    'num_participants' => 30,
    'purpose_id' => 2, // make sure this exists in requisition_purposes
    'additional_requests' => 'Audio system and stage setup',

    'formal_letter_url' => 'https://example.com/formal_letter_maria.pdf',
    'formal_letter_public_id' => 'formal_letter_maria',
    'facility_layout_url' => 'https://example.com/layout_maria.pdf',
    'facility_layout_public_id' => 'layout_maria',
    'proof_of_payment_url' => 'https://example.com/payment_maria.pdf',
    'proof_of_payment_public_id' => 'payment_maria',
    'upload_token' => Str::random(20),

    'status_id' => 8, // Completed/Closed
    'start_date' => now()->subDays(3)->toDateString(),
    'end_date' => now()->subDays(3)->toDateString(),
    'start_time' => '08:00:00',
    'end_time' => '12:00:00',

    'is_late' => false,
    'returned_at' => now()->subDays(2),

    'is_finalized' => true,
    'finalized_at' => now()->subDays(4),
    'finalized_by' => 1, // admin ID

    'tentative_fee' => 4000.00,
    'approved_fee' => 4000.00,

    'is_closed' => true,
    'closed_at' => now()->subDays(1),
    'closed_by' => 1, // admin ID

    'endorser' => 'Dean Smith',
    'date_endorsed' => now()->subDays(5),

    'created_at' => now()->subDays(10),
    'updated_at' => now()->subDays(1),
]);

// Insert requested facilities for this requisition form
DB::table('requested_facilities')->insert([
    [
        'request_id' => 6, // match the new form's ID
        'facility_id' => 1,
    ],
    [
        'request_id' => 6,
        'facility_id' => 3,
    ],
]);

// Insert requested equipment
DB::table('requested_equipment')->insert([
    [
        'request_id' => 6,
        'equipment_id' => 2,
        'quantity' => 5,
    ]
]);


    }
}
