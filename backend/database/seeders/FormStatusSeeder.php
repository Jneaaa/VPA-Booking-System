<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FormStatus;

class FormStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            'Pending Approval',  // waiting for any admin to review
            'In Review',  // someone has started reviewing
            'Awaiting Payment', // fee has been finalized
            'Scheduled',  // user has paid; booking locked in
            'Ongoing',  // current schedule is active
            'Returned', // waiting for admin to verify returns
            'Late Return', // missed deadline
            'Completed',  // done
            'Rejected', // denied by admin
            'Cancelled', // user/admin cancellation
        ];

        foreach ($statuses as $name) {
            FormStatus::firstOrCreate(['status_name' => $name]);
        }
    }
}