<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\RoomDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        // Pick a category and a matching subcategory
        $category_id = $this->faker->numberBetween(1, 5);

        $subcategory_id = match ($category_id) {
            1 => rand(1, 3),     // Buildings
            2 => rand(4, 7),     // Indoor Facilities
            3 => rand(8, 12),    // Residencies
            4 => rand(13, 16),   // Outside Spaces
            5 => rand(17, 21),   // Sports Venues
        };

        // Auto-link RoomDetail if subcategory implies a room
        $roomRequired = in_array($subcategory_id, [4, 5, 6, 7, 9, 11, 12]);
        $room_id = $roomRequired
            ? RoomDetail::where('subcategory_id', $subcategory_id)->inRandomOrder()->value('room_id')
            : null;

        return [
            'facility_name'       => $this->faker->words(2, true),
            'description'         => $this->faker->optional()->sentence(),
            'maximum_rental_hour' => $this->faker->numberBetween(1, 8),
            'category_id'         => $category_id,
            'subcategory_id'      => $subcategory_id,
            'room_id'             => $room_id,
            'location_note'       => $this->faker->address,
            'capacity'            => $this->faker->numberBetween(10, 300),
            'department_id'       => 1,
            'is_indoors' => $this->faker->randomElement(['Indoors', 'Outdoors']),
            'rental_fee'          => $this->faker->randomFloat(2, 500, 5000),
            'company_fee'         => $this->faker->randomFloat(2, 200, 2000),
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
