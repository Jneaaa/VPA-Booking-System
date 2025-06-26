<?php

namespace Database\Factories;


use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{

    protected $model = User::class;
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isInternal = $this->faker->boolean(70); // 70% internal, 30% external

        return [
            'user_type' => $isInternal ? 'Internal' : 'External',
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'contact_number' => $this->faker->optional()->numerify('09#########'),

            // for internal users (school_id); external users (org name)
            'organization_name' => $isInternal ? null : $this->faker->company,
            'school_id' => $isInternal ? $this->faker->numerify('20######') : null,

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}