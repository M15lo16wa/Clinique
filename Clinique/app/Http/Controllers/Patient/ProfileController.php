<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    // Voir son propre profil
    public function show(Request $request): JsonResponse
    {
        $utilisateur = $request->user();
        $patient = $utilisateur->patient;

        if (! $patient) {
            return response()->json(['message' => 'Profil patient introuvable. Contactez l\'administrateur.'], 404);
        }

        return response()->json([
            'profil' => array_merge(
                $patient->toArray(),
                ['email' => $utilisateur->email]
            ),
        ]);
    }

    // Mettre à jour son profil
    public function update(Request $request): JsonResponse
    {
        $utilisateur = $request->user();
        $patient = $utilisateur->patient;

        if (! $patient) {
            return response()->json(['message' => 'Profil patient introuvable. Contactez l\'administrateur.'], 404);
        }

        $request->validate([
            'nom'            => 'sometimes|string|max:100',
            'prenom'         => 'sometimes|string|max:100',
            'telephone'      => 'nullable|string|max:20',
            'adresse'        => 'sometimes|string|max:255',
            'email'          => 'sometimes|email|unique:utilisateurs,email,' . $utilisateur->id,
            'password'       => 'sometimes|string|min:6',
        ]);

        $patient->update($request->only('nom', 'prenom', 'telephone', 'adresse'));

        if ($request->has('email')) {
            $utilisateur->update(['email' => $request->email]);
        }

        if ($request->has('password')) {
            $utilisateur->update(['password' => $request->password]);
        }

        return response()->json([
            'message' => 'Profil mis à jour.',
            'profil'  => array_merge(
                $patient->fresh()->toArray(),
                ['email' => $utilisateur->fresh()->email]
            ),
        ]);
    }
}
