<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seul l'administrateur est seedé : la base métier est partagée avec
        // le site WordPress, on n'y injecte jamais de données de démonstration.
        // (Pour une base locale vide : php artisan db:seed --class=DemoDataSeeder)
        $this->call(AdminUserSeeder::class);
    }
}
