<?php

namespace Database\Factories;


use App\Models\Facility;
use App\Models\LookupTables\FacilityCategory;
use App\Models\LookupTables\FacilitySubcategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facility>
 */
class FacilityFactory extends Factory
{

    protected $model = Facility::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // 1. Pick a random category from the DB
        $category_id = FacilityCategory::inRandomOrder()->value('category_id');

        // 2. Get a random subcategory under that category
        $subcategory_id = FacilitySubcategory::where('category_id', $category_id)
            ->inRandomOrder()
            ->value('subcategory_id');

        return [
        'facility_name' => $this->faker->words(2, true), // e.g. "Science Hall"
        'description' => $this->faker->optional()->sentence(),

        'category_id' => $category_id,
        'subcategory_id' =>$subcategory_id,
        'room_id' => null, // Or 1 if you have room records

        'location_note' => $this->faker->address,
        'capacity' => $this->faker->numberBetween(10, 300),

        'department_id' => 1, // Replace with a valid department ID
        'is_indoors' => $this->faker->randomElement(['Indoors', 'Outdoors']),

        'rental_fee' => $this->faker->randomFloat(2, 500, 5000),
        'company_fee' => $this->faker->randomFloat(2, 200, 2000),

        'status_id' => 1, // Replace with valid status_id
        'created_by' => 1, // Must match existing admin ID
        'updated_by' => null,
        'deleted_by' => null,
        'last_booked_at' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),

        'created_at' => now(),
        'updated_at' => now(),

        ];
    }
}
