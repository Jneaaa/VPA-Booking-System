<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminDepartmentSeeder extends Seeder
{
    public function run()
    {
        $admin = Admin::find(1); // Example admin
        $admin->departments()->attach([
            1 => ['is_primary' => true],  // Department 1 as primary
            2 => ['is_primary' => false]  // Department 2 as secondary
        ]);
    }
}