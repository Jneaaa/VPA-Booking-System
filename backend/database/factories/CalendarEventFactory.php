<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CalendarEvent;

class CalendarEventFactory extends Factory
{
    protected $model = CalendarEvent::class;

    public function definition(): array
    {
        return [
            'request_id' => \App\Models\RequisitionForm::inRandomOrder()->value('request_id'),
            'event_title' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->sentence(6),

            'created_by' => \App\Models\Admin::inRandomOrder()->value('admin_id'),
            'updated_by' => null,
            'deleted_by' => null,

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
