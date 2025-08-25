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
            'late_penalty_fee' => null,
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
    }
}
