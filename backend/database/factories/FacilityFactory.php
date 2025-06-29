<?php

namespace Database\Factories;

use App\Models\Facility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facility>
 */
class FacilityFactory extends Factory
{
    protected static $counter = 1;
    protected $model = Facility::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Pick a category and a matching subcategory
        $category_id = $this->faker->numberBetween(1, 5);

        $subcategory_id = match ($category_id) {
            1 => rand(1, 3),     // Buildings
            2 => rand(4, 7),     // Indoor Facilities
            3 => rand(8, 12),    // Residencies
            4 => rand(13, 16),   // Outside Spaces
            5 => rand(17, 21),   // Sports Venues
        };

        return [
            'facility_name'       => 'Facility #' . self::$counter++,
            'description'         => 'Insert a short description of the facility here.',
            'maximum_rental_hour' => $this->faker->numberBetween(1, 8),
            'category_id'         => $category_id,
            'subcategory_id'      => $subcategory_id,
            'location_note'       => $this->faker->address,
            'capacity'            => $this->faker->numberBetween(10, 300),
            'department_id'       => 1,
            'is_indoors' => $this->faker->randomElement(['Indoors', 'Outdoors']),
            'internal_fee'         => $this->faker->randomFloat(2, 100, 1000),
            'external_fee'         => $this->faker->randomFloat(2, 100, 1000),
            'company_fee'          => $this->faker->randomFloat(2, 50, 500),
            'rate_type'           => $this->faker->randomElement(['Per Hour', 'Per Show/Event']),
            'status_id'           => $this->faker->numberBetween(1, 3),
            'last_booked_at'      => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'created_by'          => $this->faker->numberBetween(1, 3),
            'updated_by'          => null,
            'deleted_by'          => null,
            'created_at'          => now(),
            'updated_at'          => now(),
        ];
    }
}
