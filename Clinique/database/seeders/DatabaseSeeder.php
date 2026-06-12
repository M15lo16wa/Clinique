<?php

namespace Database\Seeders;

use App\Models\Utilisateur;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Utilisateur::factory(10)->create();

        Utilisateur::factory()->create([
            'email' => 'admin@clinique.com',
            'password' => Hash::make('administrateur@clinique.com'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
}
