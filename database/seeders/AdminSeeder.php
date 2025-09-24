<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->insert([
            [
                'first_name' => 'Hannah',
                'last_name' => 'Escosar',
                'middle_name' => 'O.',
                'role_id' => '1',
                'school_id' => '2289-09',
                'email' => 'hannahescosar@gmail.com',
                'contact_number' => '77777777',
                'hashed_password' => Hash::make('hisoka123'),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'first_name' => 'Janea',
                'last_name' => 'Geresola',
                'middle_name' => '',
                'role_id' => '4',
                'school_id' => '2249-12',
                'email' => 'janea@gmail.com',
                'contact_number' => '',
                'hashed_password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'first_name' => 'Chin',
                'last_name' => 'Dasal',
                'middle_name' => '',
                'role_id' => '1',
                'school_id' => '4153-12',
                'email' => 'chin@gmail.com',
                'contact_number' => '',
                'hashed_password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now()
            ],
         ]);
    }
}
