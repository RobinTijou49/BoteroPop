<?php

namespace Database\Factories;

use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Image>
 */
class ImageFactory extends Factory
{
    protected $model = Image::class;

    public function definition(): array
    {
        return [
            // GIF 1x1 transparent : plus petit binaire d'image valide.
            'image' => base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'),
            'nom' => ucfirst(fake()->words(3, true)),
            'description' => fake()->text(200),
            'prix' => fake()->randomFloat(2, 50, 5000),
        ];
    }
}
