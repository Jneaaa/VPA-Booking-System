<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RateTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rate_types')->insert([
            [
                'type_name' => 'Per Hour',
            ],
            [
                'type_name' => 'Per Show/Event',
            ],
         ]);
    }
}
