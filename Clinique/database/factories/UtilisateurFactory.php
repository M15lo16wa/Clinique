<?php

namespace Database\Factories;

use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UtilisateurFactory extends Factory
{
    protected $model = Utilisateur::class;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => fake()->randomElement(['patient', 'medecin', 'receptionniste', 'admin']),
            'is_active' => true,
        ];
    }
}
