<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'nom' => 'Exposition '.ucfirst(fake()->words(2, true)),
            'description' => fake()->text(200),
            'lieu' => fake()->city(),
            'date' => fake()->dateTimeBetween('-1 month', '+3 months')->format('Y-m-d'),
        ];
    }
}
