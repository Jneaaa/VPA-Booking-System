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
                'first_name' => 'John',
                'last_name' => 'Doe',
                'middle_name' => 'Smith',
                'role_id' => 1,
                'school_id' => '20-9483-38',
                'email' => 'admin@example.com',
                'contact_number' => '+63 912 345 6789',
                'hashed_password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'username' => 'HanEscob',
                'first_name' => 'Hannah',
                'last_name' => 'Escobar',
                'middle_name' => 'Oniot',
                'role_id' => 3,
                'school_id' => '18-9483-38',
                'email' => 'ccsdepartment@example.com',
                'contact_number' => '+63991234567891',
                'hashed_password' => Hash::make('hannahgwynethhehe'),
                'created_at' => now(),
                'updated_at' => now()
            ]
            
         ]);
    }
}
