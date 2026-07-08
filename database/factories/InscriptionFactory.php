<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Inscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inscription>
 */
class InscriptionFactory extends Factory
{
    protected $model = Inscription::class;

    public function definition(): array
    {
        return [
            'id_event' => Event::factory(),
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
        ];
    }
}
