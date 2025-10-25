<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExtraServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('extra_services')->insert([
            [
                'service_name' => 'Technical Support',
                'service_description' => 'Assistance with audio, visual, and technical setup during events.',
            ],
            [
                'service_name' => 'Security Personnel',
                'service_description' => 'Trained staff to ensure safety and crowd control during events.',
            ],
            [
                'service_name' => 'Logistics Assistance',
                'service_description' => 'Help with transport, setup, and event coordination logistics.',
            ],
        ]);
    }
}
