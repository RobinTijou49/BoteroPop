<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Crée le compte administrateur par défaut du Back Office.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@monsite.fr'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('Admin123!'),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
            ],
        );
    }
}
