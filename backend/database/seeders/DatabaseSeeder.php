<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ActionTypeSeeder::class,
            AdminRoleSeeder::class,
            AvailabilityStatusSeeder::class,
            ConditionSeeder::class,
            DepartmentSeeder::class,
            EquipmentCategorySeeder::class,
            AdminSeeder::class,
            AdminDepartmentSeeder::class,
            EquipmentSeeder::class,
            EquipmentItemSeeder::class,
            FacilityCategorySeeder::class,
            FacilitySubcategorySeeder::class,
            FacilitySeeder::class,
            RequisitionPurposeSeeder::class,
            FormStatusCodeSeeder::class,
            RequisitionTestSeeder::class,
            
            // Add other seeders here if needed
        ]);
    }
}
