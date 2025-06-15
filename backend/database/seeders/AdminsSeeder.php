<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->insert([
            [
                'username' => 'John Doe',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'middle_name' => 'Smith',
                'role_id' => 1,
                'school_id' => '20-9483-38',
                'email' => 'admin@school.edu',
                'contact_number' => '+63 912 345 6789',
                'hashed_password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now()
            ]
         ]);
    }
}
