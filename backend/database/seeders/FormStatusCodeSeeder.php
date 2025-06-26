<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FormStatusCode;

class FormStatusCodeSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = 
        [
            'Pending Approval',     // waiting for any admin to review
            'In Review',            // someone has started reviewing
            'Awaiting Payment',     // fee has been finalized
            'Scheduled',            // user has paid; booking locked in
            'Ongoing',              // current schedule is active
            'Returned',             // waiting for admin to verify returns
            'Late Return',          // missed deadline
            'Completed',            // done
            'Rejected',             // denied by admin
            'Cancelled',            // user/admin cancellation
        ];

        foreach ($statuses as $name) {
            FormStatusCode::firstOrCreate(['status_name' => $name]);
        }
    }
}
