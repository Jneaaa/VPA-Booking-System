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
            EquipmentSeeder::class,
            EquipmentItemSeeder::class,
            EquipmentImageSeeder::class,
            AdminDepartmentSeeder::class,
            FacilityCategorySeeder::class,
            FacilitySubcategorySeeder::class,
            FacilitySeeder::class,
            FacilityImageSeeder::class,
            RequisitionPurposeSeeder::class,
            FormStatusSeeder::class,
            RequisitionFormsSeeder::class
        ]);
    }
}
