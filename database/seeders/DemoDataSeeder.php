<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Image;
use App\Models\Inscription;
use App\Models\Tag;
use Illuminate\Database\Seeder;

/**
 * Données de démonstration. À n'exécuter QUE sur une base locale vide :
 *
 *     php artisan db:seed --class=DemoDataSeeder
 *
 * Volontairement absent de DatabaseSeeder pour ne jamais injecter de fausses
 * données dans la base partagée avec le site WordPress.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $tags = collect(['Peinture', 'Sculpture', 'Street Art', 'Photographie', 'Pop Art'])
            ->map(fn (string $nom) => Tag::firstOrCreate(['nom' => $nom]));

        Image::factory(12)
            ->create()
            ->each(function (Image $image) use ($tags) {
                $image->tags()->sync($tags->random(rand(1, 3))->pluck('id'));
            });

        Event::factory(6)
            ->create()
            ->each(function (Event $event) {
                Inscription::factory(rand(0, 8))->create(['id_event' => $event->id]);
            });
    }
}
