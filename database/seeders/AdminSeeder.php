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
                'first_name' => 'Dany',
                'last_name' => 'Molina',
                'middle_name' => 'C.',
                'title' => 'Vice President of Administration',
                'signature_url' => 'https://res.cloudinary.com/dn98ntlkd/image/upload/v1761686795/ysejb5cpn9rtuzwfkyue.png',
                'signature_public_id' => 'ysejb5cpn9rtuzwfkyue',
                'role_id' => 2,
                'school_id' => '2289-09-12',
                'email' => 'vpa@gmail.com',
                'contact_number' => '09123849503',
                'hashed_password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'R.',
                'last_name' => 'Quimba',
                'middle_name' => null,
                'title' => 'Bldgs. Supervisor',
                'signature_url' => null,
                'signature_public_id' => null,
                'role_id' => 3,
                'school_id' => '2249-12-43',
                'email' => 'officer1@gmail.com',
                'contact_number' => null,
                'hashed_password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Engr. I.L.',
                'last_name' => 'Cachopero',
                'middle_name' => null,
                'title' => 'EMS Supervisor',
                'signature_url' => null,
                'signature_public_id' => null,
                'role_id' => 3,
                'school_id' => null,
                'email' => 'officer2@gmail.com',
                'contact_number' => null,
                'hashed_password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'J.',
                'last_name' => 'Tumalay',
                'middle_name' => null,
                'title' => 'CTSSO',
                'signature_url' => null,
                'signature_public_id' => null,
                'role_id' => 3,
                'school_id' => null,
                'email' => 'officer3@gmail.com',
                'contact_number' => null,
                'hashed_password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'D.',
                'last_name' => 'Lebrilla',
                'middle_name' => null,
                'title' => 'EMC Coordinator',
                'signature_url' => null,
                'signature_public_id' => null,
                'role_id' => 3,
                'school_id' => null,
                'email' => 'officer4@gmail.com',
                'contact_number' => null,
                'hashed_password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'H.',
                'last_name' => 'Diaz',
                'middle_name' => null,
                'title' => 'Coordinator',
                'signature_url' => null,
                'signature_public_id' => null,
                'role_id' => 3,
                'school_id' => null,
                'email' => 'officer5@gmail.com',
                'contact_number' => null,
                'hashed_password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Jessica',
                'last_name' => 'Bataclit',
                'middle_name' => 'P.',
                'title' => 'Secretary',
                'signature_url' => null,
                'signature_public_id' => null,
                'role_id' => 1,
                'school_id' => null,
                'email' => 'jessicabataclit@gmail.com',
                'contact_number' => null,
                'hashed_password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Student',
                'last_name' => 'Assistant',
                'middle_name' => null,
                'title' => 'EMC Student Assistant',
                'signature_url' => null,
                'signature_public_id' => null,
                'role_id' => 4,
                'school_id' => null,
                'email' => 'emc@gmail.com',
                'contact_number' => null,
                'hashed_password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
