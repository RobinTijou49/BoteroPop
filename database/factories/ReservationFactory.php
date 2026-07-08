<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'image_id' => Image::factory(),
            'customer_name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->boolean(60) ? fake()->phoneNumber() : null,
            'status' => fake()->randomElement(array_keys(Reservation::STATUSES)),
            'reserved_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
